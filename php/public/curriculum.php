<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_CURRICULUM',
    'action' => 'VIEW',
    'email' => $email,
    'in' => ['p_email' => $email]
];
$pipeline = new EventPipeline($context);

// === DATA ===
// RECUPERO SKILL ASSOCIATE AL CURRICULUM DELL'UTENTE
$skill_utente = $pipeline->fetch_all('sp_skill_curriculum_selectAll');

// RECUPERO SKILL GLOBALI
$skill_globali = $pipeline->fetch_all('sp_skill_selectAll', []);

// RECUPERO SKILL CHE L'UTENTE NON HA ANCORA ASSOCIATO AL PROPRIO CURRICULUM
$skill_disponibili = $pipeline->fetch_all('sp_skill_curriculum_selectDiff');
?>

<!-- === PAGE ===-->
<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <!-- ALERT -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- TITOLO -->
        <h1 class="mb-4">Curriculum</h1>

        <div class="row g-4">
            <!-- SKILL CURRICULUM -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Le mie Skill</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fst-italic mb-3">Queste sono le skill attualmente associate al tuo profilo.</p>
                        <?php if ($skill_utente['failed']): ?>
                            <p class="text-center">C'è stato un errore nel recupero delle skill.</p>
                        <?php elseif (empty($skill_utente['data'])): ?>
                            <p class="text-center">Non hai ancora aggiunto nessuna skill.</p>
                        <?php else: ?>
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
                                    <?php $rank = 1; foreach ($skill_utente['data'] as $skill): ?>
                                        <tr>
                                            <td><?= $rank++; ?></td>
                                            <td><?= htmlspecialchars($skill['competenza']); ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: <?=(htmlspecialchars($skill['livello_effettivo']) * 20); ?>%">
                                                        <?= htmlspecialchars($skill['livello_effettivo']); ?>/5
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= htmlspecialchars(generate_url('curriculum_skill_update', ['competenza' => $skill['competenza'], 'livello' => $skill['livello_effettivo']])); ?>"
                                                   class="btn btn-sm btn-warning">
                                                    Modifica
                                                </a>
                                                <form action="../actions/skill_curriculum_delete.php" method="POST"
                                                      class="d-inline">
                                                    <input type="hidden" name="competenza"
                                                           value="<?= htmlspecialchars($skill['competenza']); ?>">
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- AGGIUNGI SKILL CURRICULUM -->
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
                                    <?php if ($skill_disponibili['failed']): ?>
                                        <p class="text-muted">C'è stato un errore nel recupero delle skill.</p>
                                    <?php elseif (empty($skill_disponibili['data'])): ?>
                                        <p class="text-muted">Non ci sono altre skill disponibili.</p>
                                    <?php else: ?>
                                    <select name="competenza" id="skill" class="form-select" required>
                                        <option value="">Seleziona una skill</option>
                                        <?php foreach ($skill_disponibili['data'] as $skill): ?>
                                            <option value="<?= htmlspecialchars($skill['competenza']); ?>">
                                                <?= htmlspecialchars($skill['competenza']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php endif; ?>
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

        <?php if ($_SESSION['is_admin']): ?>
        <!-- GLOBAL SKILL (ADMIN) -->
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

                            <!-- LISTA SKILL GLOBALI -->
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
                                    <?php if ($skill_globali['failed']): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-danger">C'è stato un errore nel recupero delle skill.</td>
                                        </tr>
                                    <?php elseif (empty($skill_globali['data'])): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Non ci sono skill globali.</td>
                                        </tr>
                                    <?php else: ?>
                                    <?php $rank = 1; foreach ($skill_globali['data'] as $skill): ?>
                                        <tr>
                                            <td><?= $rank++; ?></td>
                                            <td><?= htmlspecialchars($skill['competenza']); ?></td>
                                            <td class="text-end">
                                                <a href="<?= htmlspecialchars(generate_url('curriculum_skill_global_update', ['competenza' => $skill['competenza']])); ?>"
                                                   class="btn btn-sm btn-warning">
                                                    Modifica
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
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