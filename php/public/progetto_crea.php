<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO_CREA',
    'action' => 'VIEW',
    'email' => $_SESSION['email'],
    'redirect' => generate_url('home')
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN CREATORE
$pipeline->check(
    !isset($_SESSION['is_creatore']) || !$_SESSION['is_creatore'],
    "Non sei autorizzato ad effettuare questa operazione."
);
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- ALERT -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- TITOLO -->
    <h1 class="mb-4">Crea Nuovo Progetto</h1>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Informazioni Progetto</h4>
        </div>
        <div class="card-body">
            <form action="<?=generate_url('progetto_insert') ?>" method="post">

                <!-- NOME PROGETTO -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-bold">Nome Progetto</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                    <small class="form-text text-muted">Il nome del progetto deve essere unico.</small>
                </div>

                <!-- DESCRIZIONE -->
                <div class="mb-3">
                    <label for="descrizione" class="form-label fw-bold">Descrizione</label>
                    <textarea class="form-control" id="descrizione" name="descrizione" rows="4" required></textarea>
                    <small class="form-text text-muted">Descrivi il tuo progetto in dettaglio.</small>
                </div>

                <!-- BUDGET -->
                <div class="mb-3">
                    <label for="budget" class="form-label fw-bold">Budget (€)</label>
                    <input type="number" class="form-control" id="budget" name="budget" min="0.01" step="0.01" required>
                    <small class="form-text text-muted">L'importo totale necessario per il progetto.</small>
                </div>

                <!-- DATA LIMITE -->
                <div class="mb-3">
                    <label for="data_limite" class="form-label fw-bold">Data Limite</label>
                    <?php $tomorrow = date('Y-m-d', strtotime('+1 day')); ?>
                    <input type="date" class="form-control" id="data_limite" name="data_limite" min="<?= $tomorrow; ?>" value="<?= $tomorrow; ?>" required>
                    <small class="form-text text-muted">La data limite per il progetto. Deve essere futura ad oggi.</small>
                </div>

                <!-- TIPO PROGETTO -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo di Progetto</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" id="tipo_software" value="software" required>
                        <label class="form-check-label" for="tipo_software">Software</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo" id="tipo_hardware" value="hardware" required>
                        <label class="form-check-label" for="tipo_hardware">Hardware</label>
                    </div>
                    <small class="form-text text-muted">Seleziona il tipo di progetto che stai creando.</small>
                </div>

                <!-- SUBMIT -->
                <button type="submit" class="btn btn-primary">Crea Progetto</button>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>