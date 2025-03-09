<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Controllo che l'attributo sia stato specificato
if (!isset($_GET['attr']) || !isset($_GET['nome'])) {
    redirect(
        false,
        "Parametro mancante",
        "../public/progetti.php"
    );
}

// 3. L'utente è il creatore del progetto
if (!($_SESSION['is_creatore'] && checkProgettoOwner($_SESSION['email'], $_GET['nome']))) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . $_GET['nome']
    );
}

// === DATABASE ===
// Recupero il progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $progetto = sp_invoke('sp_progetto_select', $in)[0];
    if (!isset($progetto)) {
        redirect(
            false,
            "Progetto non trovato.",
            "../public/progetti.php"
        );
    }
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del progetto: " . $ex->errorInfo[2],
        "../public/progetti.php"
    );
}

// Recupero le foto del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $photos = sp_invoke('sp_foto_selectAll', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero delle foto: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Recupero le reward del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $rewards = sp_invoke('sp_reward_selectAllByProgetto', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero delle reward: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Recupero il tipo del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    // Restituisce un array di record, di cui il primo (e unico) si rappresenta come il campo testo 'tipo_progetto'
    $progetto['tipo'] = sp_invoke('sp_util_progetto_type', $in)[0]['tipo_progetto'] ?? '';
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del tipo del progetto: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_GET['nome'])
    );
}

// Se il progetto è di tipo HARDWARE, recupero il costo delle componenti
if ($progetto['tipo'] === 'HARDWARE') {
    try {
        $in = ['p_nome_progetto' => $_GET['nome']];
        $out = ['p_costo_totale_out' => 0];
        sp_invoke('sp_util_progetto_componenti_costo', $in, $out)[0]['p_costo_totale_out'] ?? 0;
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del costo delle componenti: " . $ex->errorInfo[2],
            '../public/progetti.php'
        );
    }
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!--- Tasto per tornare indietro --->
        <div class="d-flex justify-content-end">
            <button class="btn btn-warning mb-3">
                <a href="../public/progetto_dettagli.php?nome=<?php echo $_GET['nome']; ?>"
                   class="text-black text-decoration-none">Torna al Progetto</a>
            </button>
        </div>

        <?php
        switch ($_GET['attr']) {

            // Aggiorna la descrizione/foto
            case "descrizione":
                ?>
                <!-- Form per la descrizione -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Aggiorna Descrizione</h3>
                    </div>
                    <div class="card-body">
                        <form action="../actions/progetto_descrizione_update.php" method="post">
                            <input type="hidden" name="nome"
                                   value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="attr" value="descrizione">
                            <div class="form-group">
                                <label for="descrizione" class="fw-bold fs-5">Nuova Descrizione</label>
                                <textarea class="form-control my-3" id="descrizione" name="descrizione" rows="5"
                                          required><?php echo htmlspecialchars($progetto['descrizione']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Aggiorna</button>
                        </form>
                    </div>
                </div>
                <hr>
                <!-- Form per le foto -->
                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        <h3>Inserisci/Elimina Foto</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($photos)): ?>
                            <div class="card-body">
                                <div class="d-flex flex-nowrap overflow-auto">
                                    <?php foreach ($photos as $photo): ?>
                                        <div class="flex-shrink-0 w-25 px-2">
                                            <?php $base64 = base64_encode($photo['foto']); ?>
                                            <form action="../actions/foto_delete.php" method="post">
                                                <input type="hidden" name="nome" value="<?php echo $progetto['nome']; ?>">
                                                <input type="hidden" name="id" value="<?php echo $photo['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm mb-2">Elimina</button>
                                            </form>
                                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                 class="img-fluid rounded"
                                                 alt="Foto progetto">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <hr>
                        <form action="../actions/progetto_descrizione_update.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="nome"
                                   value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="attr" value="foto">
                            <div class="form-group">
                                <label for="foto" class="fw-bold fs-5">Seleziona Foto (Max 4MB)</label>
                                <input type="file" class="form-control my-3" id="foto" name="foto" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Inserisci Foto</button>
                        </form>
                    </div>
                </div>
                <?php
                break;

            // Aggiorna il budget
            case "budget":
                // Recupero il totale dei finanziamenti e calcolo la percentuale
                try {
                    $in = ['p_nome_progetto' => $_GET['nome']];
                    $totalFin = sp_invoke('sp_finanziamento_selectAllByProgetto', $in)[0]['totale_finanziamenti'] ?? 0;
                    $progetto['tot_finanziamento'] = $totalFin;
                    $progetto['percentuale'] = ($progetto['budget'] > 0) ? ($totalFin / $progetto['budget']) * 100 : 0;
                } catch (PDOException $ex) {
                    $progetto['tot_finanziamento'] = 0;
                    $progetto['percentuale'] = 0;
                }
                ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Aggiorna Budget</h3>
                    </div>
                    <div class="card-body">
                        <!-- Dettagli Finanziamenti -->
                        <div class="bg-secondary-subtle p-1 rounded text-center mb-3">
                            <p class="fs-4">
                                <strong>Budget Attuale:</strong> <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                            </p>
                            <?php if ($progetto['tipo'] === 'HARDWARE'): ?>
                                <p class="fs-5">
                                    <strong>Costo Componenti:</strong> <?php echo htmlspecialchars(number_format($out['p_costo_totale_out'], 2)); ?>€
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex w-100 fw-bold justify-content-center fs-5 mb-3">
                            <?php echo round($progetto['percentuale'], 2); ?>% Finanziato
                        </div>
                        <div class="progress mb-3 position-relative" style="height: 40px;">
                            <div class="progress-bar fw-bold bg-success"
                                 style="width: <?php echo round($progetto['percentuale'], 2); ?>%; height: 100%;">
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black fs-6">
                                <?php echo htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                                / <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                            </div>
                        </div>
                        <!-- Form per aggiornare il budget -->
                        <form action="../actions/progetto_budget_update.php" method="post">
                            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="stato" value="<?php echo htmlspecialchars($progetto['stato']); ?>">
                            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($progetto['tipo']); ?>">
                            <input type="hidden" name="attr" value="budget">
                            <div class="form-group">
                                <label for="budget" class="fw-bold">Nuovo Budget (€)</label>
                                <p class="small text-muted">
                                    Il nuovo budget non può essere inferiore al costo delle componenti
                                    (<?php echo htmlspecialchars(number_format($out['p_costo_totale_out'], 2)); ?>€)
                                </p>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="budget"
                                       name="budget" required
                                       placeholder="<?php echo htmlspecialchars($progetto['budget']); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Aggiorna Budget</button>
                        </form>
                    </div>
                </div>
                <?php
                break;

            // Aggiorna i profili (progetto software)
            case "profilo":
                ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Aggiorna Profili</h3>
                    </div>
                    <div class="card-body">
                        <form action="../public/profilo_dettagli.php" method="post">
                            <input type="hidden" name="nome"
                                   value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="attr" value="profilo">
                            <div class="form-group">
                                <label for="profilo" class="fw-bold">Gestisci Profili</label>
                                <select class="form-control" id="profilo" name="profilo">
                                    <option value="update">Aggiorna profilo esistente</option>
                                    <option value="insert">Inserisci nuovo profilo</option>
                                    <option value="delete">Elimina profilo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Continua</button>
                        </form>
                    </div>
                </div>
                <?php
                break;

            // Aggiorna le componenti (progetto hardware)
            default:
                redirect(
                    false,
                    "Attributo non valido.",
                    "../public/progetto_dettagli.php?nome=" . $_GET['nome']
                );
                break;
        }
        ?>
    </div>
<?php require '../components/footer.php'; ?>