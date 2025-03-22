<!--
/**
 * COMPONENT: error_alert (PARENT: multiple pages)
 *
 * PURPOSE:
 * - Visualizza messaggi di errore memorizzati nella sessione.
 * - Formatta i messaggi di errore con uno stile visivamente coerente.
 * - Rimuove automaticamente il messaggio di errore dalla sessione dopo la visualizzazione.
 */
-->

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
        <?= htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>