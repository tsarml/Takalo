<?php require __DIR__ . '/../partials/header.php'; ?>

<h1 class="mb-4">Tous les objets disponibles</h1>

<div class="row g-4">
    <?php foreach ($objects as $obj): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card object-card h-100">
                <?php if ($obj['photo']): ?>
                    <img src="<?= htmlspecialchars($obj['photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($obj['name']) ?>">
                <?php else: ?>
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height:180px;">
                        Pas de photo
                    </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($obj['name']) ?></h5>
                    <p class="card-text text-muted small">
                        <?= htmlspecialchars($obj['category'] ?? '—') ?> • 
                        État : <?= htmlspecialchars($obj['condition'] ?? '—') ?>
                    </p>
                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($obj['description'] ?? '', 0, 120))) ?>...</p>
                    
                    <div class="mt-auto">
                        <div class="price-estimated mb-2">
                            Valeur estimée : <?= number_format($obj['estimated_value'] ?? 0) ?> Ar
                        </div>
                        
                        <?php if ($obj['user_id'] != ($current_user_id ?? 0)): ?>
                            <button class="btn btn-outline-primary btn-sm w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exchangeModal"
                                    data-owner-id="<?= $obj['user_id'] ?>"
                                    data-item-id="<?= $obj['id'] ?>">
                                Proposer un échange
                            </button>
                        <?php else: ?>
                            <span class="badge bg-success w-100">C'est le vôtre</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal unique pour tous les échanges -->
<div class="modal fade" id="exchangeModal" tabindex="-1" aria-labelledby="exchangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exchangeModalLabel">Proposer un échange</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/exchange/propose">
                    <input type="hidden" name="target_item_id" id="target_item_id" value="">
                    
                    <h6>Vos objets disponibles pour l'échange :</h6>
                    <div id="my-objects-container" class="row g-3">
                        <!-- chargés via fetch -->
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer la proposition</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>