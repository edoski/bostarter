<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_GET(['competenza']);
$vecchia_competenza = $_GET['competenza'];
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('curriculum')
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN AMMINISTRATORE
$pipeline->check(
        !(isset($is_admin) || $is_admin),
    "Non sei autorizzato a visualizzare questa pagina",
);
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Modifica Skill Globale</h5>
            </div>
            <div class="card-body">
                <form action="../actions/skill_update.php" method="POST">
                    <input type="hidden" name="vecchia_competenza" value="<?= htmlspecialchars($vecchia_competenza); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nome Attuale</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($vecchia_competenza); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nuova_competenza" class="form-label">Nuovo Nome</label>
                        <input type="text" name="nuova_competenza" id="nuova_competenza"
                               class="form-control" required value="<?= htmlspecialchars($vecchia_competenza); ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= htmlspecialchars(generate_url('curriculum')); ?>"
                           class="btn btn-secondary">Annulla</a>
                        <button type="submit" class="btn btn-danger">Aggiorna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>