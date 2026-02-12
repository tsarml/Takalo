<?php

class EchangeController {

    private $db;

    public function __construct() {
        // On récupère la connexion PDO enregistrée dans Flight
        $this->db = Flight::db();
    }

    // Liste des propositions (pour l'utilisateur connecté)
    public function listPropositions() {
        // On suppose que l'utilisateur connecté est stocké en session
        $currentUserId = $_SESSION['user_id'] ?? null;

        if (!$currentUserId) {
            Flight::redirect('/login');
            return;
        }

        // Récupère les propositions où je suis le proposee (reçu)
        // + éventuellement celles que j'ai envoyées (optionnel)
        $stmt = $this->db->prepare("
            SELECT 
                e.id_echange,
                e.id_proposer,
                e.id_proposee,
                e.id_object_proposer,
                e.id_object_proposee,
                e.status,
                e.proposal_date,
                e.response_date,
                u1.email AS proposer_email,
                u2.email AS proposee_email,
                o1.nom AS objet_proposer,
                o2.nom AS objet_proposee
            FROM echanges e
            JOIN users u1 ON e.id_proposer = u1.id_user
            JOIN users u2 ON e.id_proposee = u2.id_user
            JOIN objects o1 ON e.id_object_proposer = o1.id_object
            JOIN objects o2 ON e.id_object_proposee = o2.id_object
            WHERE e.id_proposee = :user_id
              AND e.status = 'en cours'
            ORDER BY e.proposal_date DESC
        ");

        $stmt->execute(['user_id' => $currentUserId]);
        $propositions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On passe les données à la vue
        Flight::render('echanges/list.php', [
            'propositions' => $propositions,
            'currentUserId' => $currentUserId
        ]);
    }

    // Accepter une proposition
    public function accepter($id_echange) {
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            Flight::halt(403, 'Non autorisé');
        }

        $stmt = $this->db->prepare("
            UPDATE echanges 
            SET status = 'accepter', response_date = NOW()
            WHERE id_echange = :id 
              AND id_proposee = :user_id 
              AND status = 'en cours'
        ");

        $stmt->execute([
            'id' => $id_echange,
            'user_id' => $currentUserId
        ]);

        if ($stmt->rowCount() > 0) {
            // Optionnel : ici tu peux aussi transférer la propriété des objets
            // UPDATE objects SET id_user = ... WHERE id_object = ...
        }

        Flight::redirect('/echanges');
    }

    // Rejeter une proposition
    public function rejeter($id_echange) {
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            Flight::halt(403, 'Non autorisé');
        }

        $stmt = $this->db->prepare("
            UPDATE echanges 
            SET status = 'rejecter', response_date = NOW()
            WHERE id_echange = :id 
              AND id_proposee = :user_id 
              AND status = 'en cours'
        ");

        $stmt->execute([
            'id' => $id_echange,
            'user_id' => $currentUserId
        ]);

        Flight::redirect('/echanges');
    }
}