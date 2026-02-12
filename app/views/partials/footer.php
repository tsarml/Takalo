</main>

<footer class="bg-light text-center py-4 mt-5">
    <div class="container">
        <p class="mb-0">Takalo-takalo © <?= date('Y') ?> – Échange simple et sympa</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- Pour le modal + proposition d'échange -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const proposeBtns = document.querySelectorAll('[data-bs-toggle="modal"][data-owner-id]');
    proposeBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const ownerId = this.getAttribute('data-owner-id');
            const itemId = this.getAttribute('data-item-id');
            document.getElementById('exchangeModalLabel').textContent = `Proposer un échange pour "${this.closest('.card').querySelector('.card-title').textContent}"`;
            document.getElementById('target_item_id').value = itemId;
            // On va charger dynamiquement les objets de l'utilisateur cible
            fetch(`/objects/my?for_exchange=${ownerId}`)
                .then(r => r.text())
                .then(html => {
                    document.getElementById('my-objects-container').innerHTML = html;
                });
        });
    });
});
</script>
</body>
</html>