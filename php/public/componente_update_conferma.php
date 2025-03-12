<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_componente', 'nome_componente_originale', 'descrizione', 'quantita', 'prezzo']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// === DATABASE ===
// Recupero i dettagli del progetto
try {
    $in = ['p_nome' => $_POST['nome_progetto']];
    $progetto = sp_invoke('sp_progetto_select', $in)[0];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del progetto: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Recupero i dettagli del componente attuale
try {
    $in = ['p_nome_progetto' => $_POST['nome_progetto']];
    $componenti = sp_invoke('sp_componente_selectAllByProgetto', $in);

    $componenteAttuale = null;
    foreach ($componenti as $componente) {
        if ($componente['nome_componente'] == $_POST['nome_componente_originale']) {
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
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei componenti: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Calcolo il costo totale dei componenti (escluso quello da modificare)
$costoTotaleAltriComponenti = 0;
foreach ($componenti as $componente) {
    if ($componente['nome_componente'] != $_POST['nome_componente_originale']) {
        $costoTotaleAltriComponenti += $componente['prezzo'] * $componente['quantita'];
    }
}

// Valori attuali e nuovi
$nome_componente_originale = $_POST['nome_componente_originale'];
$nome_componente_nuovo = $_POST['nome_componente'];
$descrizione_attuale = $componenteAttuale['descrizione'];
$quantita_attuale = $componenteAttuale['quantita'];
$prezzo_attuale = $componenteAttuale['prezzo'];
$costo_attuale = $quantita_attuale * $prezzo_attuale;

$descrizione_nuova = $_POST['descrizione'];
$quantita_nuova = intval($_POST['quantita']);
$prezzo_nuovo = floatval($_POST['prezzo']);
$costo_nuovo = $quantita_nuova * $prezzo_nuovo;

// Calcolo l'impatto sul budget
$budget_attuale = $progetto['budget'];
$nuovo_costo_totale = $costoTotaleAltriComponenti + $costo_nuovo;
$differenza_costo = $costo_nuovo - $costo_attuale;

// Determina se il budget deve essere modificato
$budget_cambiera = false;
$nuovo_budget = $budget_attuale;

if ($nuovo_costo_totale > $budget_attuale) {
    $budget_cambiera = true;
    $nuovo_budget = $nuovo_costo_totale;
}
?>

<?php require '../components/header.php'; ?>
<div class="container card p-4 my-3">
    <div class="card">
        <h1 class="card-header">Conferma Modifica Componente</h1>
        <div class="card-body">
            <h4>Progetto: <strong><?php echo htmlspecialchars($progetto['nome']); ?></strong></h4>

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
                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome_componente_nuovo); ?></p>
                            <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($descrizione_nuova); ?></p>
                            <p><strong>Quantità:</strong> <?php echo htmlspecialchars($quantita_nuova); ?></p>
                            <p><strong>Prezzo:</strong> <?php echo htmlspecialchars(number_format($prezzo_nuovo, 2)); ?>€</p>
                            <p><strong>Costo Totale:</strong> <?php echo htmlspecialchars(number_format($costo_nuovo, 2)); ?>€</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert <?php echo $differenza_costo > 0 ? 'alert-warning' : 'alert-info'; ?> mt-4">
                <h5 class="alert-heading">Impatto sul Budget</h5>
                <p><strong>Budget Attuale:</strong> <?php echo htmlspecialchars(number_format($budget_attuale, 2)); ?>€</p>
                <p><strong>Costo Totale dei Componenti (dopo modifica):</strong> <?php echo htmlspecialchars(number_format($nuovo_costo_totale, 2)); ?>€</p>
                <p><strong>Differenza di Costo:</strong> <?php echo ($differenza_costo >= 0 ? '+' : ''); ?><?php echo htmlspecialchars(number_format($differenza_costo, 2)); ?>€</p>

                <?php if ($budget_cambiera): ?>
                    <hr>
                    <p class="mb-0 text-danger">
                        <strong>Attenzione:</strong> Il budget del progetto verrà aumentato da <?php echo htmlspecialchars(number_format($budget_attuale, 2)); ?>€
                        a <?php echo htmlspecialchars(number_format($nuovo_budget, 2)); ?>€ per coprire il costo dei componenti.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="../public/progetto_aggiorna.php?attr=componenti&nome=<?php echo urlencode($_POST['nome_progetto']); ?>&componente=<?php echo urlencode($_POST['nome_componente_originale']); ?>" class="btn btn-secondary">Annulla</a>

            <form action="../actions/componente_update.php" method="post">
                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_POST['nome_progetto']); ?>">
                <input type="hidden" name="nome_componente" value="<?php echo htmlspecialchars($_POST['nome_componente_originale']); ?>">
                <input type="hidden" name="nuovo_nome_componente" value="<?php echo htmlspecialchars($_POST['nome_componente']); ?>">
                <input type="hidden" name="descrizione" value="<?php echo htmlspecialchars($_POST['descrizione']); ?>">
                <input type="hidden" name="quantita" value="<?php echo htmlspecialchars($_POST['quantita']); ?>">
                <input type="hidden" name="prezzo" value="<?php echo htmlspecialchars($_POST['prezzo']); ?>">
                <button type="submit" class="btn btn-primary">Conferma Modifica</button>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>