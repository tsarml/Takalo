<?php include __DIR__ . '/../partials/header.php'; ?>

<h1>Mes propositions d'échange reçues</h1>

<?php if (empty($propositions)): ?>
    <p>Aucune proposition en cours pour le moment.</p>
<?php else: ?>
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Proposeur</th>
                <th>Objet proposé</th>
                <th>Mon objet demandé</th>
                <th>Date proposition</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($propositions as $prop): ?>
                <tr>
                    <td><?= htmlspecialchars($prop['id_echange']) ?></td>
                    <td><?= htmlspecialchars($prop['proposer_email']) ?></td>
                    <td><?= htmlspecialchars($prop['objet_proposer']) ?></td>
                    <td><?= htmlspecialchars($prop['objet_proposee']) ?></td>
                    <td><?= $prop['proposal_date'] ?></td>
                    <td><?= htmlspecialchars($prop['status']) ?></td>
                    <td>
                        <?php if ($prop['status'] === 'en cours'): ?>
                            <form method="POST" action="/echanges/<?= $prop['id_echange'] ?>/accepter" style="display:inline;">
                                <button type="submit">Accepter</button>
                            </form>
                            <form method="POST" action="/echanges/<?= $prop['id_echange'] ?>/rejeter" style="display:inline;">
                                <button type="submit">Rejeter</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>