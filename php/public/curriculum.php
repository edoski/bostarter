<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// Recupero le skill associate all'utente.
try {
    $in = ['p_email' => $_SESSION['email']];
    $skillUtente = sp_invoke('sp_skill_curriculum_selectAll', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nel recupero delle skill: " . $ex->errorInfo[2],
        '../public/home.php'
    );
}

// Recupero tutte le skill globali.
try {
    $skillGlobali = sp_invoke('sp_skill_selectAll');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nel recupero delle skill globali: " . $ex->errorInfo[2],
        '../public/home.php'
    );
}

// Recupero le skill disponibili che l'utente non ha ancora associato al proprio profilo.
try {
    $in = ['p_email' => $_SESSION['email']];
    $skillDisponibili = sp_invoke('sp_skill_curriculum_selectDiff', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nel recupero delle skill disponibili: " . $ex->errorInfo[2],
        '../public/home.php'
    );
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <!-- Messaggio di successo/errore post-azione -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- My Skills Section -->
        <h1 class="mb-4">Curriculum</h1>
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Le mie Skill</h4>
                <hr>
                <small class="text-muted fst-italic">Queste sono le skill attualmente associate al tuo profilo.</small>
            </div>
            <div class="card-body">
                <?php if (!empty($skillUtente)): ?>
                    <ul class="list-group overflow-y-auto" style="max-height: 300px;">
                        <?php $rank = 1; ?>
                        <?php foreach ($skillUtente as $skill): ?>
                            <li class="list-group-item">
                                <?php echo $rank . ". " . htmlspecialchars($skill['competenza']) . " (Livello: " . htmlspecialchars($skill['livello_effettivo']) . "/5)"; ?>
                            </li>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="mb-0">Non hai ancora aggiunto nessuna skill.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Skill Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Aggiungi Skill</h4>
                <hr>
                <small class="text-muted fst-italic">
                    Seleziona una skill disponibile e inserisci il livello che possiedi. Verrà aggiunta nel tuo curriculum di sopra.
                </small>
            </div>
            <div class="card-body">
                <form action="../actions/skill_curriculum_insert.php" method="POST">
                    <div class="mb-3">
                        <label for="skill" class="form-label">Skill Disponibile</label>
                        <select name="competenza" id="skill" class="form-select" required>
                            <option value="">Seleziona una skill</option>
                            <?php foreach ($skillDisponibili as $skill): ?>
                                <option value="<?php echo htmlspecialchars($skill['competenza']); ?>">
                                    <?php echo htmlspecialchars($skill['competenza']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="livello" class="form-label">Livello (0-5)</label>
                        <input type="number" name="livello" id="livello" class="form-control" min="0" max="5" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Aggiungi Skill</button>
                </form>
            </div>
        </div>

        <!-- Global Skills Section (Admin Only) -->
        <?php if ($_SESSION['is_admin']): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Gestisci Skill (Admin)</h4>
                    <hr>
                    <small class="text-muted fst-italic">Modifica la lista globale delle skill.</small>
                </div>
                <div class="card-body">
                    <form action="../actions/skill_insert.php" method="POST" class="mb-4">
                        <div class="mb-3">
                            <label for="new_skill" class="form-label">Nuova Skill</label>
                            <input type="text" name="competenza" id="new_skill" class="form-control" placeholder="Inserisci la nuova skill" required>
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">Aggiungi Skill Globale</button>
                    </form>

                    <h5 class="mb-3">Lista Skill Globali</h5>
                    <ul class="list-group overflow-y-auto" style="max-height: 300px;">
                        <?php $rank = 1; ?>
                        <?php foreach ($skillGlobali as $skill): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $rank . ". " . htmlspecialchars($skill['competenza']); ?>
                            </li>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>