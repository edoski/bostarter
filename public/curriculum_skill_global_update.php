<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è admin
checkAdmin();

$vecchia_competenza = $_GET['competenza'] ?? '';
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Modifica Skill Globale</h5>
            </div>
            <div class="card-body">
                <form action="../actions/skill_update.php" method="POST">
                    <input type="hidden" name="vecchia_competenza" value="<?php echo htmlspecialchars($vecchia_competenza); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nome Attuale</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vecchia_competenza); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nuova_competenza" class="form-label">Nuovo Nome</label>
                        <input type="text" name="nuova_competenza" id="nuova_competenza"
                               class="form-control" required value="<?php echo htmlspecialchars($vecchia_competenza); ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../public/curriculum.php" class="btn btn-secondary">Annulla</a>
                        <button type="submit" class="btn btn-danger">Aggiorna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>