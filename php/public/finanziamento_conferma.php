<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'importo']);
$nome_progetto = $_POST['nome'];
$importo = floatval($_POST['importo']);

// === CONTEXT ===
$context = [
    'collection' => 'FINANZIAMENTO',
    'action' => 'VIEW',
    'email' => $_SESSION['email'],
    'redirect' => generate_url('progetti')
];
$pipeline = new EventPipeline($context);

// === DATA ===
// RECUPERO I DETTAGLI DEL PROGETTO
$progetto = $pipeline->fetch('sp_progetto_select', ['p_nome' => $nome_progetto]);

// RECUPERO LE REWARD DISPONIBILI PER QUESTO IMPORTO
$rewards = $pipeline->fetch_all('sp_reward_selectAllByFinanziamentoImporto', ['p_nome_progetto' => $nome_progetto, 'p_importo' => $importo]);
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
    <div class="container card p-4 my-3">
        <div class="card">
            <h1 class="card-header">Conferma Finanziamento</h1>
            <div class="card-body">
                <p>PROGETTO: <strong><?= htmlspecialchars($progetto['nome']); ?></strong></p>
                <p>CREATORE: <strong><?= htmlspecialchars($progetto['email_creatore']); ?></strong></p>
                <p>IMPORTO: <strong><?= number_format($importo, 2); ?>€</strong></p>
            </div>
            <div class="card-footer">
                <p>Nota che per poter finanziare di nuovo <?= htmlspecialchars($progetto['nome']); ?> dovrai <strong>attendere fino a domani</strong>.</p>
            </div>
        </div>
        <hr>
        <?php if ($rewards['failed']): ?>
            <div class="alert alert-danger" role="alert">
                <p>C'è stato un errore durante il recupero delle rewards disponibili.</p>
            </div>
        <?php elseif (empty($rewards['data'])): ?>
            <p>Nessuna reward disponibile per questo importo. Impossibile procedere con il finanziamento.</p>
            <a href="<?=generate_url('progetto_dettagli', ['nome' => $nome_progetto]); ?>"
               class="btn btn-secondary">
                Torna al Progetto
            </a>
        <?php else: ?>
            <form action="<?=generate_url('finanziamento_insert') ?>" method="post">
                <input type="hidden" name="nome" value="<?= htmlspecialchars($nome_progetto); ?>">
                <input type="hidden" name="importo" value="<?= htmlspecialchars($importo); ?>">
                <div class="form-group card">
                    <div class="card-header p-2">
                        <label class="fw-bold fs-5" for="reward">Seleziona Reward</label>
                        <p class="fs-6 text-muted">Hai diritto a una sola reward per finanziamento. Seleziona di sotto quella che preferisci.</p>
                    </div>
                    <div class="row card-body">
                        <?php foreach ($rewards['data'] as $reward): ?>
                            <div class="flex-shrink-0 w-25 p-2">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header">
                                        <p class="fw-bold"><?= htmlspecialchars($reward['codice']); ?></p>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="fw-bold">
                                            Importo minimo:
                                            <?= htmlspecialchars(number_format($reward['min_importo'], 2)); ?>€
                                        </p>
                                        <p class="flex-grow-1"><?= htmlspecialchars($reward['descrizione']); ?></p>
                                        <!-- Foto della reward -->
                                        <div class="d-flex justify-content-center mt-auto">
                                            <?php $base64 = base64_encode($reward['foto']); ?>
                                            <img src="data:image/jpeg;base64,<?= $base64; ?>"
                                                 class="img-fluid rounded"
                                                 alt="Foto reward">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="reward"
                                                   id="reward_<?= htmlspecialchars($reward['codice']); ?>"
                                                   value="<?= htmlspecialchars($reward['codice']); ?>" required>
                                            <label class="form-check-label fw-bold"
                                                   for="reward_<?= htmlspecialchars($reward['codice']); ?>">
                                                Seleziona
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3"
                        onclick="return confirm('Sei sicuro di voler procedere con il finanziamento?');">
                    Conferma Finanziamento</button>
            </form>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>