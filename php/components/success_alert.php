<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center" role="alert">
        <div><?= htmlspecialchars($_SESSION['success']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
