<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// Recupero tutti i progetti
try {
    $progetti = sp_invoke('sp_progetto_selectAll');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei progetti: " . $ex->errorInfo[2],
        "../public/home.php"
    );
}


// Per ogni progetto, calcolo il tipo, il totale dei finanziamenti e i giorni rimasti alla scadenza
$today = new DateTime();
foreach ($progetti as &$progetto) {
    // Recupero il tipo del progetto
    try {
        $in = ['p_nome_progetto' => $progetto['nome']];

        // Restituisce un array di record, di cui il primo (e unico) si rappresenta come il campo testo 'tipo_progetto'
        $progetto['tipo'] = sp_invoke('sp_util_progetto_type', $in)[0]['tipo_progetto'] ?? '';
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del tipo del progetto: " . $ex->errorInfo[2],
            "../public/home.php"
        );
    }

    // Recupero il totale dei finanziamenti per il progetto
    try {
        $in = ['p_nome_progetto' => $progetto['nome']];

        // Restituisce un array di record, di cui il primo (e unico) si rappresenta come il campo numerico 'totale_finanziamenti'
        $totalFin = sp_invoke('sp_finanziamento_selectSumByProgetto', $in)[0]['totale_finanziamenti'] ?? 0;

        $progetto['tot_finanziamento'] = $totalFin;
        $budget = $progetto['budget'];
        $progetto['percentuale'] = ($budget > 0) ? ($totalFin / $budget) * 100 : 0;
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del totale dei finanziamenti: " . $ex->errorInfo[2],
            "../public/home.php"
        );
    }

    // Calcolo i giorni rimasti alla scadenza del progetto
    try {
        $scadenzaDate = new DateTime($progetto['data_limite']);
        $progetto['giorni_rimasti'] = ($today < $scadenzaDate) ? $today->diff($scadenzaDate)->days : 0;
    } catch (DateMalformedStringException $e) {
        $progetto['giorni_rimasti'] = "Errore";
    }
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <!-- Titolo e pulsante di creazione progetto / conversione creatore -->
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-4">Tutti i Progetti</h1>
            <?php if ($_SESSION['is_creatore']): ?>
                <form action="../public/progetto_crea.php">
                    <button class="btn btn-outline-primary" type="submit">Crea Progetto</button>
                </form>
            <?php else: ?>
                <form action="../actions/utente_convert_creatore.php">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>">
                    <button class="btn btn-outline-primary" type="submit">Diventa Creatore</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Messaggio di successo/errore post-azione -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <?php if (!empty($progetto)): ?>
            <div class="row">
                <?php unset($progetto); // Unsetto la variabile $progetto per evitare conflitti con il ciclo successivo
                foreach ($progetti as $progetto): ?>
                    <!-- Card Progetto -->
                    <div class="col-md-4 mb-4">
                        <a href="progetto_dettagli.php?nome=<?php echo urlencode($progetto['nome']); ?>"
                           class="text-decoration-none text-reset">
                            <div class="card h-100 shadow-sm">
                                <!-- Header: Nome Progetto e Tipo -->
                                <div class="card-header text-white bg-primary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <!-- Nome Progetto e Tipo -->
                                        <div>
                                            <h5 class="card-title mb-0 fw-bolder"><?php echo htmlspecialchars($progetto['nome']); ?></h5>
                                            <small class="text-light fw-bold"><?php echo strtoupper(htmlspecialchars($progetto['tipo'])); ?></small>
                                        </div>
                                        <!-- Status: Aperto / Chiuso -->
                                        <span class="badge p-2 fs-5 <?php echo(strtolower(htmlspecialchars($progetto['stato'])) === 'chiuso' ? 'bg-danger' : 'bg-success'); ?>">
                                        <?php echo strtoupper(htmlspecialchars($progetto['stato'])); ?>
                                    </span>
                                    </div>
                                </div>

                                <!-- Body: Descrizione, Creatore, Budget e Progress Bar -->
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <!-- Creatore -->
                                        <p class="card-text">
                                            <strong>Creatore:</strong> <?php echo htmlspecialchars($progetto['email_creatore']); ?>
                                        </p>
                                        <!-- Budget -->
                                        <p class="card-text">
                                            <strong>Budget:</strong> <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                                        </p>
                                        <!-- Descrizione -->
                                        <p class="card-text"><?php echo htmlspecialchars($progetto['descrizione']); ?></p>
                                    </div>
                                    <div>
                                        <!-- Percentuale di completamento -->
                                        <div class="d-flex w-100 fw-bold justify-content-center">
                                            <?php echo round($progetto['percentuale'], 2); ?>%
                                        </div>

                                        <!-- Barra di progresso Finanziamenti / Budget -->
                                        <div class="progress mt-2 position-relative" style="height: 30px;">
                                            <div class="progress-bar fw-bold bg-success"
                                                 style="width: <?php echo round($progetto['percentuale'], 2); ?>%; height: 100%;">
                                            </div>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black">
                                                <?php echo htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                                                / <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer: Data Inserimento, Scadenza e Giorni Rimasti -->
                                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column justify-content-center">
                                        <small>
                                            Durata: <?php echo htmlspecialchars(date('d/m/Y', strtotime($progetto['data_inserimento']))); ?>
                                            - <?php echo htmlspecialchars(date('d/m/Y', strtotime($progetto['data_limite']))); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php if ($progetto['stato'] === 'aperto'): ?>
                                            <span class="badge bg-dark-subtle text-dark-emphasis">
                                            <?php echo htmlspecialchars($progetto['giorni_rimasti']); ?> GIORNI RIMASTI</span>
                                        <?php else: ?>
                                            <span class="badge bg-dark-subtle text-dark-emphasis">TERMINATO</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Nessun progetto trovato.</p>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>