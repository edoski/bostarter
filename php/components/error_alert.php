<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
        <?= htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>