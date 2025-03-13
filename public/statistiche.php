<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// Controllo se l'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// Classifica dei top 3 utenti creatori, in base al loro valore di affidabilità
try {
    $creatori = sp_invoke('view_classifica_creatori_affidabilita');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei creatori: " . $ex->errorInfo[2],
        "../public/home.php"
    );
}

// Classifica dei top 3 progetti APERTI che sono più vicini al proprio completamento
try {
    $progetti = sp_invoke('view_classifica_progetti_completamento');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei progetti: " . $ex->errorInfo[2],
        "../public/home.php"
    );
}

// Classifica dei top 3 utenti, in base al TOTALE di finanziamenti erogati
try {
    $finanziatori = sp_invoke('view_classifica_utenti_finanziamento');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei finanziatori: " . $ex->errorInfo[2],
        "../public/home.php"
    );
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4 flex-grow-1">
        <!-- Messaggio di successo/errore post-azione -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

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
                            <?php $rank = 1; ?>
                            <?php foreach ($creatori as $creatore): ?>
                                <li class="list-group-item">
                                    <?php echo $rank . ". " . htmlspecialchars($creatore['nickname']); ?>
                                </li>
                                <?php $rank++; ?>
                            <?php endforeach; ?>
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
                            <?php $rank = 1; ?>
                            <?php foreach ($progetti as $progetto): ?>
                                <li class="list-group-item my-1">
                                    <div class="d-flex">
                                        <!-- Dettagli dei Progetti -->
                                        <div class="flex-grow-1">
                                            <div>
                                                <?php echo $rank . ". " . htmlspecialchars($progetto['nome']); ?>
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
                                <?php $rank++; ?>
                            <?php endforeach; ?>
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
                            <?php $rank = 1; ?>
                            <?php foreach ($finanziatori as $finanziatore): ?>
                                <li class="list-group-item">
                                    <?php echo $rank . ". " . htmlspecialchars($finanziatore['nickname']); ?>
                                </li>
                                <?php $rank++; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>