<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. Controllo che l'utente sia autenticato
checkAuth();

// 2. Controllo che i dati siano stati inviati correttamente
if (!isset($_POST['nome']) || !isset($_POST['importo'])) {
    redirect(
        false,
        "Dati finanziamento mancanti.",
        "../public/progetti.php"
    );
}

// === DATABASE ===
$nome_progetto = $_POST['nome'];
$importo = floatval($_POST['importo']);

// Recupero i dettagli del progetto tramite sp_progetto_select
try {
    $in = ['p_nome' => $nome_progetto];
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

// Recupero le reward disponibili in base all'importo donato
try {
    $in = ['p_nome_progetto' => $nome_progetto, 'p_importo' => $importo];
    $rewards = sp_invoke('sp_reward_selectAllByFinanziamentoImporto', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero delle reward: " . $ex->errorInfo[2],
        "progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}
?>

<?php require '../components/header.php'; ?>
    <div class="container card p-4 my-3">
        <div class="card">
            <h1 class="card-header">Conferma Finanziamento</h1>
            <div class="card-body">
                <p>PROGETTO: <strong><?php echo htmlspecialchars($progetto['nome']); ?></strong></p>
                <p>CREATORE: <strong><?php echo htmlspecialchars($progetto['email_creatore']); ?></strong></p>
                <p>IMPORTO: <strong><?php echo number_format($importo, 2); ?>€</strong></p>
            </div>
            <div class="card-footer">
                <p>Nota che per poter finanziare di nuovo <?php echo htmlspecialchars($progetto['nome']); ?> dovrai <strong>attendere fino a domani</strong>.</p>
            </div>
        </div>
        <hr>
        <?php if (!empty($rewards)): ?>
            <form action="../actions/finanziamento_insert.php" method="post">
                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nome_progetto); ?>">
                <input type="hidden" name="importo" value="<?php echo htmlspecialchars($importo); ?>">
                <div class="form-group card">
                    <div class="card-header p-2">
                        <label class="fw-bold fs-5" for="reward">Seleziona Reward</label>
                        <p class="fs-6 text-muted">Hai diritto a una sola reward per finanziamento. Seleziona di sotto quella che preferisci.</p>
                    </div>
                    <div class="row card-body">
                        <?php foreach ($rewards as $reward): ?>
                            <div class="flex-shrink-0 w-25 p-2">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <p class="fw-bold"><?php echo htmlspecialchars($reward['codice']); ?></p>
                                    </div>
                                    <div class="card-body">
                                        <p class="fw-bold">
                                            Importo minimo:
                                            <?php echo htmlspecialchars(number_format($reward['min_importo'], 2)); ?>€
                                        </p>
                                        <p><?php echo htmlspecialchars($reward['descrizione']); ?></p>
                                        <!-- Foto della reward -->
                                        <div class="d-flex justify-content-center">
                                            <?php $base64 = base64_encode($reward['foto']); ?>
                                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                 class="img-fluid rounded"
                                                 alt="Foto reward">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="reward"
                                                   id="reward_<?php echo htmlspecialchars($reward['codice']); ?>"
                                                   value="<?php echo htmlspecialchars($reward['codice']); ?>" required>
                                            <label class="form-check-label fw-bold"
                                                   for="reward_<?php echo htmlspecialchars($reward['codice']); ?>">
                                                Seleziona
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Conferma Finanziamento</button>
            </form>
        <?php else: ?>
            <p>Nessuna reward disponibile per questo importo. Impossibile procedere con il finanziamento.</p>
            <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($nome_progetto); ?>"
               class="btn btn-secondary">
                Torna al Progetto
            </a>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>