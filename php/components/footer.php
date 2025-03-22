<!--
/**
 * COMPONENT: footer (PARENT: all pages)
 *
 * PURPOSE:
 * - Fornisce il piè di pagina standard presente in tutte le pagine dell'applicazione.
 * - Carica gli script JavaScript di Bootstrap necessari per la funzionalità dell'interfaccia.
 */
-->

<script src="../public/libs/bootstrap.bundle.min.js"></script>
</body>
<footer class="bg-light text-dark mt-auto">
    <div class="bg-dark text-center text-white py-3">
        &copy; <?= date('Y'); ?> <a href="<?=generate_url('index') ?>">BOSTARTER</a>. All rights reserved.
    </div>
</footer>
</html>
