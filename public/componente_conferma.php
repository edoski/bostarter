<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_componente', 'descrizione', 'quantita', 'prezzo']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// Determina se stiamo aggiornando o inserendo
$is_update = isset($_POST['nome_componente_originale']);

// === DATABASE ===
// Recupero i dettagli del progetto
try {
    $in = ['p_nome' => $_POST['nome_progetto']];
    $progetto = sp_invoke('sp_progetto_select', $in)[0];
    $budget_progetto = $progetto['budget'];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del progetto: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Recupero i componenti del progetto
try {
    $in = ['p_nome_progetto' => $_POST['nome_progetto']];
    $componenti = sp_invoke('sp_componente_selectAllByProgetto', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei componenti: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Valori del nuovo componente
$nome_componente = $_POST['nome_componente'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);
$costo_nuovo = $quantita * $prezzo;

// Calcoli specifici per l'aggiornamento
if ($is_update) {
    $nome_componente_originale = $_POST['nome_componente_originale'];

    // Trova il componente attuale
    $componenteAttuale = null;
    foreach ($componenti as $componente) {
        if ($componente['nome_componente'] == $nome_componente_originale) {
            $componenteAttuale = $componente;
            break;
        }
    }

    if (!$componenteAttuale) {
        redirect(
            false,
            "Componente non trovato.",
            "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_POST['nome_progetto'])
        );
    }

    // Valori attuali
    $descrizione_attuale = $componenteAttuale['descrizione'];
    $quantita_attuale = $componenteAttuale['quantita'];
    $prezzo_attuale = $componenteAttuale['prezzo'];
    $costo_attuale = $quantita_attuale * $prezzo_attuale;

    // Calcolo costo totale escluso il componente da modificare
    $costo_totale_altri = 0;
    foreach ($componenti as $componente) {
        if ($componente['nome_componente'] != $nome_componente_originale) {
            $costo_totale_altri += $componente['prezzo'] * $componente['quantita'];
        }
    }

    $nuovo_costo_totale = $costo_totale_altri + $costo_nuovo;
    $differenza_costo = $costo_nuovo - $costo_attuale;
} else {
    // Calcoli per inserimento
    $costo_totale_altri = 0;
    foreach ($componenti as $componente) {
        $costo_totale_altri += $componente['prezzo'] * $componente['quantita'];
    }

    $nuovo_costo_totale = $costo_totale_altri + $costo_nuovo;
    $differenza_costo = $costo_nuovo;
}

// Determina se il budget cambierà
$budget_cambiera = $nuovo_costo_totale > $budget_progetto;
$nuovo_budget = $budget_cambiera ? $nuovo_costo_totale : $budget_progetto;
?>

<?php require '../components/header.php'; ?>
<div class="container card p-4 my-3">
    <div class="card">
        <h1 class="card-header">
            Conferma <?php echo $is_update ? 'Modifica' : 'Inserimento'; ?> Componente
        </h1>
        <div class="card-body">
            <h4>Progetto: <strong><?php echo htmlspecialchars($progetto['nome']); ?></strong></h4>

            <?php if ($is_update): ?>
                <!-- Vista per modifica componente -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Valori Attuali</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome_componente_originale); ?></p>
                                <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($descrizione_attuale); ?></p>
                                <p><strong>Quantità:</strong> <?php echo htmlspecialchars($quantita_attuale); ?></p>
                                <p><strong>Prezzo:</strong> <?php echo htmlspecialchars(number_format($prezzo_attuale, 2)); ?>€</p>
                                <p><strong>Costo Totale:</strong> <?php echo htmlspecialchars(number_format($costo_attuale, 2)); ?>€</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Nuovi Valori</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome_componente); ?></p>
                                <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($descrizione); ?></p>
                                <p><strong>Quantità:</strong> <?php echo htmlspecialchars($quantita); ?></p>
                                <p><strong>Prezzo:</strong> <?php echo htmlspecialchars(number_format($prezzo, 2)); ?>€</p>
                                <p><strong>Costo Totale:</strong> <?php echo htmlspecialchars(number_format($costo_nuovo, 2)); ?>€</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Vista per inserimento componente -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Nuovo Componente</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome_componente); ?></p>
                        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($descrizione); ?></p>
                        <p><strong>Quantità:</strong> <?php echo htmlspecialchars($quantita); ?></p>
                        <p><strong>Prezzo:</strong> <?php echo htmlspecialchars(number_format($prezzo, 2)); ?>€</p>
                        <p><strong>Costo Totale:</strong> <?php echo htmlspecialchars(number_format($costo_nuovo, 2)); ?>€</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="alert <?php echo $budget_cambiera ? 'alert-warning' : 'alert-info'; ?> mt-4">
                <h5 class="alert-heading">Impatto sul Budget</h5>
                <p><strong>Budget Attuale:</strong> <?php echo htmlspecialchars(number_format($budget_progetto, 2)); ?>€</p>
                <p><strong>Costo Totale dei Componenti (dopo modifica):</strong> <?php echo htmlspecialchars(number_format($nuovo_costo_totale, 2)); ?>€</p>

                <?php if ($is_update): ?>
                    <p><strong>Differenza di Costo:</strong> <?php echo ($differenza_costo >= 0 ? '+' : ''); ?><?php echo htmlspecialchars(number_format($differenza_costo, 2)); ?>€</p>
                <?php endif; ?>

                <?php if ($budget_cambiera): ?>
                    <hr>
                    <p class="mb-0 text-danger">
                        <strong>Attenzione:</strong> Il budget del progetto verrà aumentato da <?php echo htmlspecialchars(number_format($budget_progetto, 2)); ?>€
                        a <?php echo htmlspecialchars(number_format($nuovo_budget, 2)); ?>€ per coprire il costo dei componenti.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <?php if ($is_update): ?>
                <a href="../public/progetto_aggiorna.php?attr=componenti&nome=<?php echo urlencode($_POST['nome_progetto']); ?>&componente=<?php echo urlencode($_POST['nome_componente_originale']); ?>" class="btn btn-secondary">Annulla</a>
            <?php else: ?>
                <a href="../public/progetto_aggiorna.php?attr=componenti&nome=<?php echo urlencode($_POST['nome_progetto']); ?>" class="btn btn-secondary">Annulla</a>
            <?php endif; ?>

            <form action="../actions/<?php echo $is_update ? 'componente_update' : 'componente_insert'; ?>.php" method="post">
                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome_progetto']); ?>">
                <input type="hidden" name="nome_componente" value="<?php echo htmlspecialchars($nome_componente); ?>">
                <?php if ($is_update): ?>
                    <input type="hidden" name="nuovo_nome_componente" value="<?php echo htmlspecialchars($nome_componente); ?>">
                <?php endif; ?>
                <input type="hidden" name="descrizione" value="<?php echo htmlspecialchars($descrizione); ?>">
                <input type="hidden" name="quantita" value="<?php echo htmlspecialchars($quantita); ?>">
                <input type="hidden" name="prezzo" value="<?php echo htmlspecialchars($prezzo); ?>">
                <?php if ($is_update): ?>
                    <button type="submit" class="btn btn-primary">Conferma Modifica</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Conferma Inserimento</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>