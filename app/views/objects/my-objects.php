<?php foreach ($my_objects as $item): ?>
    <div class="col-6 col-md-4">
        <div class="card card-sm">
            <?php if ($item['photo']): ?>
                <img src="<?= htmlspecialchars($item['photo']) ?>" class="card-img-top" style="height:120px; object-fit:cover;">
            <?php endif; ?>
            <div class="card-body p-2">
                <p class="card-title small mb-1 fw-bold"><?= htmlspecialchars($item['name']) ?></p>
                <p class="small text-muted mb-1"><?= number_format($item['estimated_value'] ?? 0) ?> Ar</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="my_item_id" 
                           value="<?= $item['id'] ?>" 
                           id="myitem_<?= $item['id'] ?>" required>
                    <label class="form-check-label small" for="myitem_<?= $item['id'] ?>">
                        Proposer cet objet
                    </label>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if (empty($my_objects)): ?>
    <p class="text-muted">Vous n'avez aucun objet disponible pour l'échange.</p>
<?php endif; ?>