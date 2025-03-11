<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Controllo che i parametri necessari siano stati forniti
if (!isset($_POST['nome']) || !isset($_POST['profilo']) || !isset($_POST['attr'])) {
    redirect(
        false,
        "Parametri mancanti",
        "../public/progetti.php"
    );
}

// 3. L'utente è il creatore del progetto
if (!($_SESSION['is_creatore'] && checkProgettoOwner($_SESSION['email'], $_POST['nome']))) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// 4. Il progetto è di tipo software
try {
    $in = ['p_nome_progetto' => $_POST['nome']];
    // Restituisce un array di record, di cui il primo (e unico) rappresenta il campo testo 'tipo_progetto'
    $tipoProgetto = sp_invoke('sp_util_progetto_type', $in)[0]['tipo_progetto'] ?? '';

    if ($tipoProgetto !== 'SOFTWARE') {
        redirect(
            false,
            "Questa operazione è disponibile solo per progetti di tipo software.",
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del tipo del progetto: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// === DATABASE ===
// Recupero i profili esistenti del progetto (se necessario)
$profili = [];
$competenzeGlobali = [];

try {
    // Recupero tutti i profili del progetto
    $in = ['p_nome_progetto' => $_POST['nome']];
    $result = sp_invoke('sp_profilo_skill_selectAllByProgetto', $in);

    // Organizzare i risultati per profilo
    foreach ($result as $row) {
        if (!isset($profili[$row['nome_profilo']])) {
            $profili[$row['nome_profilo']] = [];
        }
        $profili[$row['nome_profilo']][] = [
            'competenza' => $row['competenza'],
            'livello' => $row['livello_richiesto']
        ];
    }

    // Recupero tutte le competenze disponibili
    $competenzeGlobali = sp_invoke('sp_skill_selectAll');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei profili esistenti: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// Determino l'operazione richiesta
$operazione = $_POST['profilo'];

// Se è un'operazione di update o delete, ma non ci sono profili, reindirizzo
if (($operazione === 'update' || $operazione === 'delete') && empty($profili)) {
    redirect(
        false,
        "Nessun profilo esistente da " . ($operazione === 'update' ? "aggiornare" : "eliminare") . ".",
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . $_POST['nome']
    );
}

// Se è stata inviata una richiesta di aggiornamento profilo
$profiloSelezionato = '';
$competenzeSelezionate = [];

if (isset($_POST['nome_profilo']) && $operazione === 'update') {
    $profiloSelezionato = $_POST['nome_profilo'];

    // Recupero le competenze associate al profilo selezionato
    if (isset($profili[$profiloSelezionato])) {
        $competenzeSelezionate = $profili[$profiloSelezionato];
    }
}
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- Messaggio di successo/errore post-azione -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- Tornare indietro -->
    <div class="d-flex justify-content-end mb-3">
        <a href="../public/progetto_aggiorna.php?attr=profilo&nome=<?php echo urlencode($_POST['nome']); ?>" class="btn btn-secondary">
            Torna indietro
        </a>
    </div>

    <!-- Card principale -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3>
                <?php
                if ($operazione === 'insert') echo "Inserisci Nuovo Profilo";
                elseif ($operazione === 'update') echo "Aggiorna Profilo";
                else echo "Elimina Profilo";
                ?>
            </h3>
        </div>
        <div class="card-body">
            <?php if ($operazione === 'insert'): ?>
                <!-- INSERIMENTO NUOVO PROFILO -->
                <form action="../actions/profilo_insert.php" method="post">
                    <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome']); ?>">

                    <div class="mb-3">
                        <label for="nome_profilo" class="form-label fw-bold">Nome Profilo</label>
                        <input type="text" class="form-control" id="nome_profilo" name="nome_profilo" required
                               placeholder="Es. Frontend Developer">
                    </div>

                    <hr>

                    <h4 class="mb-3">Competenze Richieste</h4>
                    <p class="text-muted">Seleziona le competenze richieste per questo profilo e il livello necessario (0-5).</p>

                    <div id="competenze-container">
                        <div class="row mb-3 competenza-row">
                            <div class="col-md-6">
                                <label class="form-label">Competenza</label>
                                <select name="competenze[]" class="form-select" required>
                                    <option value="">Seleziona una competenza</option>
                                    <?php foreach ($competenzeGlobali as $competenza): ?>
                                        <option value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                            <?php echo htmlspecialchars($competenza['competenza']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Livello Richiesto (0-5)</label>
                                <input type="number" name="livelli[]" class="form-control" required min="0" max="5" value="3">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger rimuovi-competenza" disabled>-</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" id="aggiungi-competenza" class="btn btn-success">
                            Aggiungi Competenza
                        </button>
                    </div>

                    <hr>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Crea Profilo</button>
                    </div>
                </form>

            <?php elseif ($operazione === 'update'): ?>
                <!-- AGGIORNAMENTO PROFILO ESISTENTE -->
                <?php if (empty($profiloSelezionato)): ?>
                    <!-- Form per selezionare il profilo da aggiornare -->
                    <form action="profilo_dettagli.php" method="post">
                        <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
                        <input type="hidden" name="profilo" value="update">
                        <input type="hidden" name="attr" value="profilo">

                        <div class="mb-3">
                            <label for="nome_profilo" class="form-label fw-bold">Seleziona Profilo da Aggiornare</label>
                            <select name="nome_profilo" id="nome_profilo" class="form-select" required>
                                <option value="">Seleziona un profilo</option>
                                <?php foreach (array_keys($profili) as $nomeProfilo): ?>
                                    <option value="<?php echo htmlspecialchars($nomeProfilo); ?>">
                                        <?php echo htmlspecialchars($nomeProfilo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Continua</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Form per aggiornare le competenze del profilo selezionato -->
                    <h4 class="mb-3">Profilo: <?php echo htmlspecialchars($profiloSelezionato); ?></h4>

                    <div class="mb-4">
                        <!-- Competenze attuali del profilo -->
                        <h5 class="mb-3">Competenze Attuali</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Competenza</th>
                                    <th>Livello</th>
                                    <th>Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($competenzeSelezionate)): ?>
                                    <?php foreach ($competenzeSelezionate as $competenza): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($competenza['competenza']); ?></td>
                                            <td><?php echo htmlspecialchars($competenza['livello']); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal" data-bs-target="#updateSkillModal"
                                                            data-competenza="<?php echo htmlspecialchars($competenza['competenza']); ?>"
                                                            data-livello="<?php echo htmlspecialchars($competenza['livello']); ?>">
                                                        Modifica
                                                    </button>
                                                    <form action="../actions/skill_profilo_delete.php" method="post" class="d-inline">
                                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
                                                        <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                                                        <input type="hidden" name="competenza" value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger ms-1"
                                                                onclick="return confirm('Sei sicuro di voler eliminare questa competenza dal profilo?')">
                                                            Elimina
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Nessuna competenza associata a questo profilo</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form per aggiungere nuove competenze al profilo -->
                    <h5 class="mb-3">Aggiungi Nuova Competenza</h5>
                    <form action="../actions/skill_profilo_insert.php" method="post">
                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
                        <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="competenza" class="form-label">Competenza</label>
                                <select name="competenza" id="competenza" class="form-select" required>
                                    <option value="">Seleziona una competenza</option>
                                    <?php
                                    // Filtra le competenze che non sono già associate al profilo
                                    $competenzeEsistenti = array_column($competenzeSelezionate, 'competenza');
                                    foreach ($competenzeGlobali as $competenza):
                                        if (!in_array($competenza['competenza'], $competenzeEsistenti)):
                                            ?>
                                            <option value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                                <?php echo htmlspecialchars($competenza['competenza']); ?>
                                            </option>
                                        <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="livello" class="form-label">Livello Richiesto (0-5)</label>
                                <input type="number" name="livello" id="livello" class="form-control" required min="0" max="5" value="3">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">Aggiungi</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                <!-- ELIMINAZIONE PROFILO -->
                <p class="text-danger fw-bold">Attenzione: L'eliminazione di un profilo comporta la rimozione di tutte le candidature associate ad esso.</p>
                <form action="../actions/profilo_delete.php" method="post">
                    <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome']); ?>">

                    <div class="mb-3">
                        <label for="nome_profilo" class="form-label fw-bold">Seleziona Profilo da Eliminare</label>
                        <select name="nome_profilo" id="nome_profilo" class="form-select" required>
                            <option value="">Seleziona un profilo</option>
                            <?php foreach (array_keys($profili) as $nomeProfilo): ?>
                                <option value="<?php echo htmlspecialchars($nomeProfilo); ?>">
                                    <?php echo htmlspecialchars($nomeProfilo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Sei sicuro di voler eliminare questo profilo? Tutte le candidature associate verranno rimosse.')">
                            Elimina Profilo
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal per l'aggiornamento del livello di una competenza -->
<div class="modal fade" id="updateSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Aggiorna Livello Competenza</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/skill_profilo_update.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome']); ?>">
                    <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                    <input type="hidden" name="competenza" id="update_competenza">

                    <div class="mb-3">
                        <label for="competenza_nome" class="form-label">Competenza</label>
                        <input type="text" class="form-control" id="competenza_nome" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nuovo_livello" class="form-label">Nuovo Livello Richiesto (0-5)</label>
                        <input type="number" name="nuovo_livello" id="nuovo_livello" class="form-control"
                               required min="0" max="5">
                        <div class="form-text text-danger">
                            Attenzione: L'aumento del livello richiesto potrebbe comportare il rifiuto automatico di candidature esistenti.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-warning">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script per la gestione dinamica delle competenze e del modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestione aggiunta/rimozione competenze per inserimento nuovo profilo
        const aggiungiCompetenzaBtn = document.getElementById('aggiungi-competenza');
        const competenzeContainer = document.getElementById('competenze-container');

        if (aggiungiCompetenzaBtn && competenzeContainer) {
            aggiungiCompetenzaBtn.addEventListener('click', function() {
                const competenzeRows = document.querySelectorAll('.competenza-row');
                const lastRow = competenzeRows[competenzeRows.length - 1];
                const newRow = lastRow.cloneNode(true);

                // Reset dei valori nel nuovo elemento
                const select = newRow.querySelector('select');
                const input = newRow.querySelector('input');
                select.value = '';
                input.value = '3';

                // Abilita il pulsante di rimozione su tutte le righe
                const allRemoveBtns = document.querySelectorAll('.rimuovi-competenza');
                allRemoveBtns.forEach(btn => {
                    btn.disabled = false;
                });

                // Aggiungi event listener per il pulsante di rimozione
                const removeBtn = newRow.querySelector('.rimuovi-competenza');
                removeBtn.addEventListener('click', function() {
                    newRow.remove();

                    // Se rimane una sola riga, disabilita il suo pulsante di rimozione
                    const remainingRows = document.querySelectorAll('.competenza-row');
                    if (remainingRows.length === 1) {
                        remainingRows[0].querySelector('.rimuovi-competenza').disabled = true;
                    }
                });

                competenzeContainer.appendChild(newRow);
            });

            // Aggiungi event listener ai pulsanti di rimozione esistenti
            const removeBtns = document.querySelectorAll('.rimuovi-competenza');
            removeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    btn.closest('.competenza-row').remove();

                    // Se rimane una sola riga, disabilita il suo pulsante di rimozione
                    const remainingRows = document.querySelectorAll('.competenza-row');
                    if (remainingRows.length === 1) {
                        remainingRows[0].querySelector('.rimuovi-competenza').disabled = true;
                    }
                });
            });
        }

        // Gestione modal per aggiornamento livello competenza
        const updateSkillModal = document.getElementById('updateSkillModal');
        if (updateSkillModal) {
            updateSkillModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const competenza = button.getAttribute('data-competenza');
                const livello = button.getAttribute('data-livello');

                const updateCompetenzaInput = document.getElementById('update_competenza');
                const competenzaNomeInput = document.getElementById('competenza_nome');
                const nuovoLivelloInput = document.getElementById('nuovo_livello');

                updateCompetenzaInput.value = competenza;
                competenzaNomeInput.value = competenza;
                nuovoLivelloInput.value = livello;
            });
        }
    });
</script>

<?php require '../components/footer.php'; ?>