<?php

/**
 * ObjectController
 * Gère la liste des objets, les propositions d'échange et leur acceptation.
 */
class ObjectController
{
    // ──────────────────────────────────────────────────────────
    //  Seuil de tolérance pour la comparaison de prix (20 %)
    // ──────────────────────────────────────────────────────────
    const PRICE_TOLERANCE = 0.20;

    // ══════════════════════════════════════════════════════════
    //  GET /objects  — Liste des objets
    // ══════════════════════════════════════════════════════════
    public function index()
    {
        // Vérifier la session
        if (!isset($_SESSION['user'])) {
            Flight::redirect('/login');
            return;
        }

        $currentUserId = (int) $_SESSION['user']['id'];
        $viewMode      = Flight::request()->query['view'] ?? 'others'; // 'mine' ou 'others'

        $db = Flight::db();

        // ── Récupérer les objets à afficher ──────────────────
        if ($viewMode === 'mine') {
            $stmt = $db->prepare(
                "SELECT id, name, photo, `condition`, category, description, estimated_price
                   FROM objects
                  WHERE owner_id = :uid
                  ORDER BY created_at DESC"
            );
            $stmt->execute([':uid' => $currentUserId]);
        } else {
            // Tous les objets SAUF les miens (disponibles uniquement)
            $stmt = $db->prepare(
                "SELECT o.id, o.name, o.photo, o.condition, o.category,
                        o.description, o.estimated_price,
                        u.name AS owner_name
                   FROM objects o
                   JOIN users u ON u.id = o.owner_id
                  WHERE o.owner_id != :uid
                    AND o.status = 'available'
                  ORDER BY o.created_at DESC"
            );
            $stmt->execute([':uid' => $currentUserId]);
        }
        $objects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── Mes objets disponibles (pour la popup d'échange) ─
        $stmtMine = $db->prepare(
            "SELECT id, name, photo, `condition`, category, estimated_price
               FROM objects
              WHERE owner_id = :uid
                AND status   = 'available'
              ORDER BY name ASC"
        );
        $stmtMine->execute([':uid' => $currentUserId]);
        $myObjects = $stmtMine->fetchAll(PDO::FETCH_ASSOC);

        // ── Rendre la vue ────────────────────────────────────
        Flight::render('objects/list', [
            'pageTitle' => $viewMode === 'mine' ? 'Mes objets' : 'Objets disponibles',
            'objects'   => $objects,
            'myObjects' => $myObjects,
            'viewMode'  => $viewMode,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  POST /exchanges/propose  — Proposer un échange (AJAX)
    //  Body JSON : { target_object_id, my_object_id }
    // ══════════════════════════════════════════════════════════
    public function propose()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $currentUserId = (int) $_SESSION['user']['id'];
        $body          = Flight::request()->data;

        $targetObjectId = (int) ($body['target_object_id'] ?? 0);
        $myObjectId     = (int) ($body['my_object_id']     ?? 0);

        if (!$targetObjectId || !$myObjectId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
            return;
        }

        $db = Flight::db();

        // ── Charger l'objet cible ────────────────────────────
        $stmtTarget = $db->prepare(
            "SELECT id, owner_id, name, estimated_price, status
               FROM objects
              WHERE id = :id"
        );
        $stmtTarget->execute([':id' => $targetObjectId]);
        $target = $stmtTarget->fetch(PDO::FETCH_ASSOC);

        if (!$target || $target['status'] !== 'available') {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Objet cible introuvable ou non disponible.']);
            return;
        }

        // ── Charger mon objet ────────────────────────────────
        $stmtMine = $db->prepare(
            "SELECT id, owner_id, name, estimated_price, status
               FROM objects
              WHERE id = :id AND owner_id = :uid"
        );
        $stmtMine->execute([':id' => $myObjectId, ':uid' => $currentUserId]);
        $mine = $stmtMine->fetch(PDO::FETCH_ASSOC);

        if (!$mine || $mine['status'] !== 'available') {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Votre objet est introuvable ou non disponible.']);
            return;
        }

        // ── Comparaison de prix ──────────────────────────────
        $priceCheck = self::comparePrices(
            (float) $mine['estimated_price'],
            (float) $target['estimated_price']
        );

        // ── Vérifier qu'un échange identique n'existe pas déjà
        $stmtDup = $db->prepare(
            "SELECT id FROM exchanges
              WHERE proposed_object_id = :po
                AND requested_object_id = :ro
                AND status = 'pending'"
        );
        $stmtDup->execute([':po' => $myObjectId, ':ro' => $targetObjectId]);
        if ($stmtDup->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Une proposition identique est déjà en attente.']);
            return;
        }

        // ── Insérer la proposition d'échange ─────────────────
        $stmtInsert = $db->prepare(
            "INSERT INTO exchanges
                (proposer_id, receiver_id, proposed_object_id, requested_object_id, status, created_at)
             VALUES
                (:proposer, :receiver, :proposed, :requested, 'pending', NOW())"
        );
        $stmtInsert->execute([
            ':proposer'  => $currentUserId,
            ':receiver'  => $target['owner_id'],
            ':proposed'  => $myObjectId,
            ':requested' => $targetObjectId,
        ]);

        $exchangeId = $db->lastInsertId();

        echo json_encode([
            'success'     => true,
            'message'     => 'Proposition envoyée avec succès !',
            'exchange_id' => $exchangeId,
            'price_check' => $priceCheck,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  POST /exchanges/accept  — Accepter un échange (AJAX)
    //  Body JSON : { exchange_id }
    // ══════════════════════════════════════════════════════════
    public function accept()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non authentifié.']);
            return;
        }

        $currentUserId = (int) $_SESSION['user']['id'];
        $body          = Flight::request()->data;
        $exchangeId    = (int) ($body['exchange_id'] ?? 0);

        if (!$exchangeId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID d\'échange manquant.']);
            return;
        }

        $db = Flight::db();

        // ── Charger l'échange ────────────────────────────────
        $stmt = $db->prepare(
            "SELECT e.*,
                    op.owner_id AS proposed_owner,
                    or2.owner_id AS requested_owner
               FROM exchanges e
               JOIN objects op  ON op.id  = e.proposed_object_id
               JOIN objects or2 ON or2.id = e.requested_object_id
              WHERE e.id = :eid AND e.status = 'pending'"
        );
        $stmt->execute([':eid' => $exchangeId]);
        $exchange = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exchange) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Échange introuvable ou déjà traité.']);
            return;
        }

        // Seul le destinataire peut accepter
        if ($exchange['receiver_id'] !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Action non autorisée.']);
            return;
        }

        // ── Transaction : transfert de propriété ─────────────
        $db->beginTransaction();
        try {
            // L'objet proposé passe au receiver
            $db->prepare(
                "UPDATE objects SET owner_id = :new_owner, status = 'exchanged' WHERE id = :oid"
            )->execute([
                ':new_owner' => $exchange['receiver_id'],
                ':oid'       => $exchange['proposed_object_id'],
            ]);

            // L'objet demandé passe au proposer
            $db->prepare(
                "UPDATE objects SET owner_id = :new_owner, status = 'exchanged' WHERE id = :oid"
            )->execute([
                ':new_owner' => $exchange['proposer_id'],
                ':oid'       => $exchange['requested_object_id'],
            ]);

            // Marquer l'échange comme accepté
            $db->prepare(
                "UPDATE exchanges SET status = 'accepted', updated_at = NOW() WHERE id = :eid"
            )->execute([':eid' => $exchangeId]);

            // Annuler les autres propositions en attente sur ces 2 objets
            $db->prepare(
                "UPDATE exchanges
                    SET status = 'cancelled', updated_at = NOW()
                  WHERE id != :eid
                    AND status = 'pending'
                    AND (proposed_object_id IN (:p, :r) OR requested_object_id IN (:p, :r))"
            )->execute([
                ':eid' => $exchangeId,
                ':p'   => $exchange['proposed_object_id'],
                ':r'   => $exchange['requested_object_id'],
            ]);

            $db->commit();

            echo json_encode(['success' => true, 'message' => 'Échange accepté ! Les objets ont changé de propriétaire.']);

        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'échange : ' . $e->getMessage()]);
        }
    }


    private static function comparePrices(float $myPrice, float $targetPrice): array
    {
        if ($targetPrice == 0) {
            return ['status' => 'ok', 'message' => 'Pas de prix estimatif pour l\'objet cible.'];
        }

        $diff    = $myPrice - $targetPrice;
        $diffPct = abs($diff) / $targetPrice;

        if ($diffPct <= self::PRICE_TOLERANCE) {
            return [
                'status'   => 'ok',
                'diff'     => $diff,
                'diff_pct' => round($diffPct * 100, 1),
                'message'  => 'Les prix sont équilibrés.',
            ];
        }

        if ($diff > 0) {
            return [
                'status'   => 'advantage_mine',
                'diff'     => $diff,
                'diff_pct' => round($diffPct * 100, 1),
                'message'  => sprintf(
                    'Votre objet est estimé %.0f %% plus cher.',
                    $diffPct * 100
                ),
            ];
        }

        return [
            'status'   => 'advantage_target',
            'diff'     => $diff,
            'diff_pct' => round($diffPct * 100, 1),
            'message'  => sprintf(
                'L\'objet demandé est estimé %.0f %% plus cher que le vôtre.',
                $diffPct * 100
            ),
        ];
    }
}