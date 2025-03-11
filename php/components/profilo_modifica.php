<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <?php echo empty($profiloSelezionato) ? "Nuovo Profilo" : "Modifica: " . htmlspecialchars($profiloSelezionato); ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($competenzaSelezionata)): ?>
                <!-- Form per modificare una competenza specifica -->
                <?php require '../components/profilo_competenza_modifica.php'; ?>
            <?php elseif (empty($profiloSelezionato)): ?>
                <!-- Form per creare un nuovo profilo -->
                <?php require '../components/profilo_crea_nuovo.php'; ?>
            <?php else: ?>
                <!-- Interfaccia per modificare un profilo esistente -->
                <?php require '../components/profilo_modifica_esistente.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</div>