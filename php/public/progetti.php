<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];
$is_creatore = $_SESSION['is_creatore'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTI',
    'action' => 'VIEW',
    'email' => $email,
];
$pipeline = new EventPipeline($context);

// === DATA ===
// RECUPERO TUTTI I PROGETTI
$progetti = $pipeline->fetch_all('sp_progetto_selectAll');

foreach ($progetti['data'] as &$progetto) {
    $in = ['p_nome_progetto' => $progetto['nome']];

    // TIPO DEL PROGETTO
    $progetto['tipo'] = $pipeline->fetch('sp_util_progetto_type', $in)['tipo_progetto'];

    // SOMMA DEI FINANZIAMENTI E PERCENTUALE DI COMPLETAMENTO
    $progetto['tot_finanziamento'] = $pipeline->fetch('sp_finanziamento_selectSumByProgetto', $in)['totale_finanziamenti'];
    $progetto['percentuale'] = ($progetto['tot_finanziamento'] / $progetto['budget']) * 100;

    // GIORNI RIMASTI ALLA SCADENZA
    try {
        $today = new DateTime();
        $data_scadenza = new DateTime($progetto['data_limite']);
        $progetto['giorni_rimasti'] = ($today < $data_scadenza) ? $today->diff($data_scadenza)->days : 0;
    } catch (Exception $e) {
        $progetto['giorni_rimasti'] = "Error";
    }
}

// === RENDERING ===
/**
 * Renderizza la card di un progetto nella lista progetti.
 *
 * @param array $progetto Dati del progetto
 * @return string HTML della card progetto
 */
function render_progetto_card(array $progetto): string
{
    ob_start();
    ?>
    <a href="<?= htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $progetto['nome']])); ?>"
       class="text-decoration-none text-reset">
        <div class="card h-100 shadow-sm">
            <div class="card-header text-white bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bolder"><?= htmlspecialchars($progetto['nome']); ?></h5>
                        <small class="text-light fw-bold"><?= strtoupper(htmlspecialchars($progetto['tipo'])); ?></small>
                    </div>
                    <span class="badge p-2 fs-5 <?=(strtolower(htmlspecialchars($progetto['stato'])) === 'chiuso' ? 'bg-danger' : 'bg-success'); ?>">
                    <?= strtoupper(htmlspecialchars($progetto['stato'])); ?>
                </span>
                </div>
            </div>

            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <p class="card-text">
                        <strong>Creatore:</strong> <?= htmlspecialchars($progetto['email_creatore']); ?>
                    </p>
                    <p class="card-text">
                        <strong>Budget:</strong> <?= htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                    </p>
                    <p class="card-text"><?= htmlspecialchars($progetto['descrizione']); ?></p>
                </div>
                <div>
                    <div class="d-flex w-100 fw-bold justify-content-center">
                        <?= round($progetto['percentuale'], 2); ?>%
                    </div>

                    <div class="progress mt-2 position-relative" style="height: 30px;">
                        <div class="progress-bar fw-bold bg-success"
                             style="width: <?= round($progetto['percentuale'], 2); ?>%; height: 100%;">
                        </div>
                        <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black">
                            <?= htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                            / <?= htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer: Data Inserimento, Scadenza e Giorni Rimasti -->
            <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column justify-content-center">
                    <small>
                        Durata: <?= htmlspecialchars(date('d/m/Y', strtotime($progetto['data_inserimento']))); ?>
                        - <?= htmlspecialchars(date('d/m/Y', strtotime($progetto['data_limite']))); ?>
                    </small>
                </div>
                <div>
                    <?php if ($progetto['stato'] === 'aperto'): ?>
                        <span class="badge bg-dark-subtle text-dark-emphasis">
                        <?= htmlspecialchars($progetto['giorni_rimasti']); ?> GIORNI RIMASTI</span>
                    <?php else: ?>
                        <span class="badge bg-dark-subtle text-dark-emphasis">TERMINATO</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </a>
    <?php
    return ob_get_clean();
}

?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center">
        <!-- TITOLO -->
        <h1 class="mb-4">Progetti</h1>

        <!-- BOTTONE CREA PROGETTO / DIVENTA CREATORE -->
        <?php if ($is_creatore): ?>
            <form action="<?= htmlspecialchars(generate_url('progetto_crea')); ?>">
                <button class="btn btn-outline-primary" type="submit">Crea Progetto</button>
            </form>
        <?php else: ?>
            <form action="../actions/utente_convert_creatore.php">
                <input type="hidden" name="email" value="<?= $email; ?>">
                <button class="btn btn-outline-primary" onclick="return confirm('Sei sicuro di voler diventare un creatore?')"
                        type="submit">Diventa Creatore</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- ALERT -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- PROGETTI -->
    <?php if ($progetti['failed']): ?>
        <p>Errore durante il recupero dei progetti.</p>
    <?php elseif (empty($progetti['data'])): ?>
        <p>Nessun progetto trovato.</p>
    <?php else: ?>
        <div class="row">
            <?php
            unset($progetto); // IMPORTANT: Unsetto la variabile $progetto per evitare conflitti con il ciclo successivo
            foreach ($progetti['data'] as $progetto): ?>
                <div class="col-md-4 mb-4">
                    <?= render_progetto_card($progetto); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require '../components/footer.php'; ?>