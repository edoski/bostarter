<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_GET(['competenza', 'livello']);
$competenza = $_GET['competenza'];
$livello = $_GET['livello'];
$email = $_SESSION['email'];
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Modifica Livello Skill</h5>
        </div>
        <div class="card-body">
            <form action="<?=generate_url('skill_curriculum_update') ?>" method="POST">
                <input type="hidden" name="competenza" value="<?= htmlspecialchars($competenza); ?>">

                <div class="mb-3">
                    <label class="form-label">Competenza</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($competenza); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="livello" class="form-label">Livello (0-5)</label>
                    <input type="number" name="livello" id="livello" class="form-control"
                           min="0" max="5" required value="<?= htmlspecialchars($livello); ?>">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?=generate_url('curriculum'); ?>"
                       class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Sei sicuro di voler aggiornare il livello della competenza? Candidature esistenti potrebbero essere rimosse.')">
                        Aggiorna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>