<?php
// === CONFIG ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_componente', 'nome_componente_originale', 'descrizione', 'quantita', 'prezzo']);
$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$nome_componente_originale = $_POST['nome_componente_originale'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMPONENTE',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto, 'componente' => $nome_componente_originale]),
    'in' => ['p_nome' => $nome_progetto]
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    'Non sei autorizzato a visualizzare questa pagina.'
);

// === DATA ===
// RECUPERO I DATI DEL PROGETTO
$progetto = $pipeline->fetch('sp_progetto_select');
$budget_progetto = $progetto['budget'];

// RECUPERO I COMPONENTI DEL PROGETTO
$componenti = $pipeline->fetch_all('sp_componente_selectAllByProgetto');

// COSTO DEL COMPONENTE AGGIORNATO
$costo_nuovo = $quantita * $prezzo;

// TROVA IL COMPONENTE DA MODIFICARE
$componente_attuale = null;
foreach ($componenti['data'] as $componente) {
    if ($componente['nome_componente'] == $nome_componente_originale) {
        $componente_attuale = $componente;
        break;
    }
}

// LANCIO ERRORE SE IL COMPONENTE NON È STATO TROVATO
$pipeline->check(
    !$componente_attuale,
    "Componente non trovato."
);

// VALORI ATTUALI DEL COMPONENTE
$descrizione_attuale = $componente_attuale['descrizione'];
$quantita_attuale = $componente_attuale['quantita'];
$prezzo_attuale = $componente_attuale['prezzo'];
$costo_attuale = $quantita_attuale * $prezzo_attuale;

// COSTO TOTALE DEGLI ALTRI COMPONENTI
$costo_totale_altri = 0;
foreach ($componenti['data'] as $componente) {
    if ($componente['nome_componente'] != $nome_componente_originale) {
        $costo_totale_altri += $componente['prezzo'] * $componente['quantita'];
    }
}

// COSTO TOTALE DEI COMPONENTI DOPO MODIFICA
$nuovo_costo_totale = $costo_totale_altri + $costo_nuovo;
$differenza_costo = $costo_nuovo - $costo_attuale;

// DETERMINO SE IL BUDGET CAMBIERÀ
$budget_diff = $nuovo_costo_totale > $budget_progetto;
$nuovo_budget = $budget_diff ? $nuovo_costo_totale : $budget_progetto;
?>

<!-- === PAGE ===-->
<?php require '../components/header.php'; ?>
<div class="container card p-4 my-3">
    <div class="card">
        <h1 class="card-header">Conferma Modifica Componente</h1>
        <div class="card-body">
            <h4>Progetto: <strong><?= htmlspecialchars($progetto['nome']); ?></strong></h4>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Valori Attuali</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nome:</strong> <?= htmlspecialchars($nome_componente_originale); ?></p>
                            <p><strong>Descrizione:</strong> <?= htmlspecialchars($descrizione_attuale); ?></p>
                            <p><strong>Quantità:</strong> <?= htmlspecialchars($quantita_attuale); ?></p>
                            <p><strong>Prezzo:</strong> <?= htmlspecialchars(number_format($prezzo_attuale, 2)); ?>€</p>
                            <p><strong>Costo Totale:</strong> <?= htmlspecialchars(number_format($costo_attuale, 2)); ?>€</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Nuovi Valori</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nome:</strong> <?= htmlspecialchars($nome_componente); ?></p>
                            <p><strong>Descrizione:</strong> <?= htmlspecialchars($descrizione); ?></p>
                            <p><strong>Quantità:</strong> <?= htmlspecialchars($quantita); ?></p>
                            <p><strong>Prezzo:</strong> <?= htmlspecialchars(number_format($prezzo, 2)); ?>€</p>
                            <p><strong>Costo Totale:</strong> <?= htmlspecialchars(number_format($costo_nuovo, 2)); ?>€</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert <?= $budget_diff ? 'alert-warning' : 'alert-info'; ?> mt-4">
                <h5 class="alert-heading">Impatto sul Budget</h5>
                <p><strong>Budget Attuale:</strong> <?= htmlspecialchars(number_format($budget_progetto, 2)); ?>€</p>
                <p><strong>Costo Totale dei Componenti (dopo modifica):</strong> <?= htmlspecialchars(number_format($nuovo_costo_totale, 2)); ?>€</p>
                <p><strong>Differenza di Costo:</strong> <?= ($differenza_costo >= 0 ? '+' : ''); ?><?= htmlspecialchars(number_format($differenza_costo, 2)); ?>€</p>
                <?php if ($budget_diff): ?>
                    <hr>
                    <p class="mb-0 text-danger">
                        <strong>Attenzione:</strong> Il budget del progetto verrà aumentato da <?= htmlspecialchars(number_format($budget_progetto, 2)); ?>€
                        a <?= htmlspecialchars(number_format($nuovo_budget, 2)); ?>€ per coprire il costo dei componenti.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="<?= htmlspecialchars(generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto, 'componente' => $nome_componente_originale])); ?>"
               class="btn btn-secondary">Annulla</a>

            <form action="../actions/componente_update.php" method="post">
                <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($nome_progetto); ?>">
                <input type="hidden" name="nome_componente" value="<?= htmlspecialchars($nome_componente_originale); ?>">
                <input type="hidden" name="nuovo_nome_componente" value="<?= htmlspecialchars($nome_componente); ?>">
                <input type="hidden" name="descrizione" value="<?= htmlspecialchars($descrizione); ?>">
                <input type="hidden" name="quantita" value="<?= htmlspecialchars($quantita); ?>">
                <input type="hidden" name="prezzo" value="<?= htmlspecialchars($prezzo); ?>">
                <button type="submit" class="btn btn-primary">Conferma Modifica</button>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>