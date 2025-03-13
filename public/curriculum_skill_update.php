<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

$competenza = $_GET['competenza'] ?? '';
$livello = $_GET['livello'] ?? 3;
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Modifica Livello Skill</h5>
        </div>
        <div class="card-body">
            <form action="../actions/skill_curriculum_update.php" method="POST">
                <input type="hidden" name="competenza" value="<?php echo htmlspecialchars($competenza); ?>">

                <div class="mb-3">
                    <label class="form-label">Competenza</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($competenza); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="livello" class="form-label">Livello (0-5)</label>
                    <input type="number" name="livello" id="livello" class="form-control"
                           min="0" max="5" required value="<?php echo htmlspecialchars($livello); ?>">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../public/curriculum.php" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-warning">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>