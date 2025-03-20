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
?>

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
                <div class="card rounded shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bolder">
                        Top 3 Creatori (Affidabilità)
                    </div>
                    <div class="card-body">
                        <p class="card-text text-center fst-italic">
                            Classifica dei top 3 utenti creatori, in base al loro valore di affidabilità.
                        </p>
                        <hr class="mb-4 border-4">
                        <ul class="list-group list-group-flush">
                            <?php if ($creatori['failed']): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-danger">
                                        Errore nel caricamento dei dati.
                                    </div>
                                </li>
                            <?php elseif (empty($creatori['data'])): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-muted">
                                        Nessun dato disponibile.
                                    </div>
                                </li>
                            <?php else: ?>
                                <?php $rank = 1; ?>
                                <?php foreach ($creatori['data'] as $creatore): ?>
                                    <li class="list-group-item">
                                        <?php echo $rank++ . ". " . htmlspecialchars($creatore['nickname']); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!--  Top 3 Progetti (Completamento) -->
            <div class="col-md-4 mb-3">
                <div class="card rounded shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bolder">
                        Top 3 Progetti (Completamento)
                    </div>
                    <div class="card-body">
                        <p class="card-text text-center fst-italic">
                            Classifica dei top 3 progetti aperti più vicini al completamento del budget.
                        </p>
                        <hr class="mb-4 border-4">
                        <ul class="list-group list-group-flush">
                            <?php if ($progetti['failed']): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-danger">
                                        Errore nel caricamento dei dati.
                                    </div>
                                </li>
                            <?php elseif (empty($progetti['data'])): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-muted">
                                        Nessun dato disponibile.
                                    </div>
                                </li>
                            <?php else: ?>
                            <?php $rank = 1; ?>
                                <?php foreach ($progetti['data'] as $progetto): ?>
                                    <li class="list-group-item my-1">
                                        <div class="d-flex">
                                            <!-- Dettagli dei Progetti -->
                                            <div class="flex-grow-1">
                                                <div>
                                                    <?php echo $rank++ . ". " . htmlspecialchars($progetto['nome']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($progetto['tot_finanziamenti']) . " / " . htmlspecialchars($progetto['budget']) ?>€
                                                </div>
                                            </div>
                                            <!-- Blue Badge con % Completamento -->
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars(number_format(($progetto['tot_finanziamenti'] / $progetto['budget']) * 100, 2)) . "%"; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!--  Top 3 Utenti (Finanziamenti) -->
            <div class="col-md-4 mb-3">
                <div class="card rounded shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bolder">
                        Top 3 Utenti (Finanziamenti)
                    </div>
                    <div class="card-body">
                        <p class="card-text text-center fst-italic">
                            Classifica dei top 3 utenti, in base al totale dei finanziamenti erogati.
                        </p>
                        <hr class="mb-4 border-4">
                        <ul class="list-group list-group-flush">
                            <?php if ($finanziatori['failed']): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-danger">
                                        Errore nel caricamento dei dati.
                                    </div>
                                </li>
                            <?php elseif (empty($finanziatori['data'])): ?>
                                <li class="list-group-item">
                                    <div class="text-center text-muted">
                                        Nessun dato disponibile.
                                    </div>
                                </li>
                            <?php else: ?>
                                <?php $rank = 1; ?>
                                <?php foreach ($finanziatori['data'] as $finanziatore): ?>
                                    <li class="list-group-item">
                                        <?php echo $rank++ . ". " . htmlspecialchars($finanziatore['nickname']); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>