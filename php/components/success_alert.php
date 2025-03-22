<!--
/**
 * COMPONENT: success_alert (PARENT: multiple pages)
 *
 * PURPOSE:
 * - Visualizza messaggi di successo memorizzati nella sessione.
 * - Formatta i messaggi di successo con uno stile visivamente coerente.
 * - Rimuove automaticamente il messaggio di successo dalla sessione dopo la visualizzazione.
 */
-->

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center" role="alert">
        <div><?= htmlspecialchars($_SESSION['success']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
