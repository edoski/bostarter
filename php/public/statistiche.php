<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === CONTEXT ===
$context = [
    'collection' => 'STATISTICHE',
    'action' => 'VIEW',
    'email' => $_SESSION['email']
];
$pipeline = new EventPipeline($context);

// === DATA ===
// TOP 3 CREATORI (AFFIDABILITÀ)
$creatori = $pipeline->fetch_all('view_classifica_creatori_affidabilita');

// TOP 3 PROGETTI (COMPLETAMENTO)
$progetti = $pipeline->fetch_all('view_classifica_progetti_completamento');

// TOP 3 UTENTI (FINANZIAMENTI)
$finanziatori = $pipeline->fetch_all('view_classifica_utenti_finanziamento');

// === RENDERING ===
/**
 * Renderizza una card statistica.
 *
 * @param string $titolo Titolo della statistica
 * @param string $descrizione Descrizione della statistica
 * @param array $dati Dati della statistica
 * @return string HTML della card
 */
function render_statistica_card(string $titolo, string $descrizione, array $dati): string
{
    ob_start();
    ?>
    <div class="card rounded shadow-sm">
        <div class="card-header bg-primary text-white text-center fw-bolder">
            <?= htmlspecialchars($titolo); ?>
        </div>
        <div class="card-body">
            <p class="card-text text-center fst-italic mb-3">
                <?= htmlspecialchars($descrizione); ?>
            </p>
            <hr class="mb-4 border-4">
            <ul class="list-group list-group-flush">
                <?php if (isset($dati['failed']) && $dati['failed']): ?>
                    <li class="list-group-item">
                        <div class="text-center text-danger">
                            Errore nel caricamento dei dati.
                        </div>
                    </li>
                <?php elseif (empty($dati['data'])): ?>
                    <li class="list-group-item">
                        <div class="text-center text-muted">
                            Nessun dato disponibile.
                        </div>
                    </li>
                <?php else: ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($dati['data'] as $item): ?>
                        <li class="list-group-item">
                            <?php if (isset($item['nickname'])): ?>
                                <?= $rank++ . ". " . htmlspecialchars($item['nickname']); ?>
                            <?php elseif (isset($item['nome'])): ?>
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <div>
                                            <?= $rank++ . ". " . htmlspecialchars($item['nome']); ?>
                                        </div>
                                        <?php if (isset($item['tot_finanziamenti']) && isset($item['budget'])): ?>
                                            <div class="text-muted small">
                                                <?= htmlspecialchars($item['tot_finanziamenti']) . " / " . htmlspecialchars($item['budget']) ?>€
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (isset($item['tot_finanziamenti']) && isset($item['budget'])): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary">
                                                <?= htmlspecialchars(number_format(($item['tot_finanziamenti'] / $item['budget']) * 100, 2)) . "%"; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?= $rank++ . ". " . htmlspecialchars(print_r($item, true)); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4 flex-grow-1">
    <!-- ALERT -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- TITOLO -->
    <h1 class="mb-4">Statistiche</h1>

    <div class="row">
        <!-- Top 3 Creatori (Affidabilità) -->
        <div class="col-md-4 mb-3">
            <?= render_statistica_card(
                'Top 3 Creatori (Affidabilità)',
                'Classifica dei top 3 utenti creatori, in base al loro valore di affidabilità.',
                $creatori
            ); ?>
        </div>

        <!--  Top 3 Progetti (Completamento) -->
        <div class="col-md-4 mb-3">
            <?= render_statistica_card(
                'Top 3 Progetti (Completamento)',
                'Classifica dei top 3 progetti aperti più vicini al completamento del budget.',
                $progetti
            ); ?>
        </div>

        <!--  Top 3 Utenti (Finanziamenti) -->
        <div class="col-md-4 mb-3">
            <?= render_statistica_card(
                'Top 3 Utenti (Finanziamenti)',
                'Classifica dei top 3 utenti, in base al totale dei finanziamenti erogati.',
                $finanziatori
            ); ?>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>