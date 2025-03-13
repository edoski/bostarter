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

        <h1 class="mb-4">Curriculum</h1>

        <!-- Top row -->
        <div class="row g-4">
            <!-- My Skills Section -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Le mie Skill</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fst-italic mb-3">Queste sono le skill attualmente associate al tuo
                            profilo.</p>

                        <?php if (!empty($skillUtente)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width=10%;">#</th>
                                        <th style="width=30%;">Competenza</th>
                                        <th style="width=30%;">Livello</th>
                                        <th class="text-end" style="width: 30%;">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $rank = 1; ?>
                                    <?php foreach ($skillUtente as $skill): ?>
                                        <tr>
                                            <td><?php echo $rank++; ?></td>
                                            <td><?php echo htmlspecialchars($skill['competenza']); ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: <?php echo(htmlspecialchars($skill['livello_effettivo']) * 20); ?>%">
                                                        <?php echo htmlspecialchars($skill['livello_effettivo']); ?>/5
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="../public/curriculum_skill_update.php?competenza=<?php echo urlencode($skill['competenza']); ?>&livello=<?php echo $skill['livello_effettivo']; ?>"
                                                   class="btn btn-sm btn-warning">
                                                    Modifica
                                                </a>
                                                <form action="../actions/skill_curriculum_delete.php" method="POST"
                                                      class="d-inline">
                                                    <input type="hidden" name="competenza"
                                                           value="<?php echo htmlspecialchars($skill['competenza']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Sei sicuro di voler rimuovere questa skill? Candidature che dipendono da essa verranno annullate.')">
                                                        Rimuovi
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center">Non hai ancora aggiunto nessuna skill.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle row - Add Skill Section -->
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Aggiungi Skill</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fst-italic mb-3">
                            Seleziona una skill disponibile e inserisci il livello che possiedi.
                        </p>
                        <form action="../actions/skill_curriculum_insert.php" method="POST">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="skill" class="form-label fw-bold">Skill Disponibile</label>
                                    <select name="competenza" id="skill" class="form-select" required>
                                        <option value="">Seleziona una skill</option>
                                        <?php foreach ($skillDisponibili as $skill): ?>
                                            <option value="<?php echo htmlspecialchars($skill['competenza']); ?>">
                                                <?php echo htmlspecialchars($skill['competenza']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="livello" class="form-label fw-bold">Livello (0-5)</label>
                                    <input type="number" name="livello" id="livello" class="form-control" min="0"
                                           max="5" required>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">Aggiungi Skill</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom row - Global Skills Section (Admin Only) -->
        <?php if ($_SESSION['is_admin']): ?>
            <div class="row g-4 mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0">Gestisci Skill (Admin)</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted fst-italic mb-3">
                                Aggiungi o modifica le skill disponibili globalmente per tutti gli utenti.
                            </p>

                            <!-- Add Global Skill -->
                            <form action="../actions/skill_insert.php" method="POST" class="mb-4">
                                <div class="row">
                                    <div class="col-md-9 mb-3">
                                        <label for="new_skill" class="form-label fw-bold">Nuova Skill Globale</label>
                                        <input type="text" name="competenza" id="new_skill" class="form-control"
                                               placeholder="Inserisci la nuova skill" required>
                                    </div>
                                    <div class="col-md-3 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-danger w-100">Aggiungi Skill Globale
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <!-- Global Skills List -->
                            <h5 class="fw-bold mb-3">Skill Globali</h5>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th style="width=10%;">#</th>
                                        <th style="width=65%;">Competenza</th>
                                        <th style="width=25%;" class="text-end">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $rank = 1; ?>
                                    <?php foreach ($skillGlobali as $skill): ?>
                                        <tr>
                                            <td><?php echo $rank++; ?></td>
                                            <td><?php echo htmlspecialchars($skill['competenza']); ?></td>
                                            <td class="text-end">
                                                <a href="../public/curriculum_skill_global_update.php?competenza=<?php echo urlencode($skill['competenza']); ?>"
                                                   class="btn btn-sm btn-warning">
                                                    Modifica
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>