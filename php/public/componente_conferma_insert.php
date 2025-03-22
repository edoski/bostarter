<?php
/**
 * PAGE: componente_conferma_insert
 *
 * ACTIONS: componente_insert
 *
 * LEADS: progetto_aggiorna
 *
 * PURPOSE:
 * - Conferma l'inserimento di un nuovo componente per un progetto hardware.
 * - Mostra l'impatto del nuovo componente sul budget del progetto.
 * - Avverte se il budget dovrà essere aumentato per coprire il costo del nuovo componente.
 * - Richiede conferma finale prima di procedere con l'inserimento.
 */

// === CONFIG ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_componente', 'descrizione', 'quantita', 'prezzo']);
$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMPONENTE',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto]),
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

// COSTO DEL NUOVO COMPONENTE
$costo_nuovo = $quantita * $prezzo;

// SOMMA DEI COSTI DEI COMPONENTI ATTUALI
$costo_totale_altri = 0;
foreach ($componenti['data'] as $componente) {
    $costo_totale_altri += $componente['prezzo'] * $componente['quantita'];
}

// IMPATTO DEL NUOVO COMPONENTE SUL BUDGET
$nuovo_costo_totale = $costo_totale_altri + $costo_nuovo;
$budget_diff = $nuovo_costo_totale > $budget_progetto;
$nuovo_budget = $budget_diff ? $nuovo_costo_totale : $budget_progetto;
?>

<!-- === PAGE ===-->
<?php require '../components/header.php'; ?>
<div class="container card p-4 my-3">
    <div class="card">
        <h1 class="card-header">Conferma Inserimento Componente</h1>
        <div class="card-body">
            <h4>Progetto: <strong><?= htmlspecialchars($progetto['nome']); ?></strong></h4>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nuovo Componente</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nome:</strong> <?= htmlspecialchars($nome_componente); ?></p>
                    <p><strong>Descrizione:</strong> <?= htmlspecialchars($descrizione); ?></p>
                    <p><strong>Quantità:</strong> <?= htmlspecialchars($quantita); ?></p>
                    <p><strong>Prezzo:</strong> <?= htmlspecialchars(number_format($prezzo, 2)); ?>€</p>
                    <p><strong>Costo Totale:</strong> <?= htmlspecialchars(number_format($costo_nuovo, 2)); ?>€</p>
                </div>
            </div>

            <div class="alert <?= $budget_diff ? 'alert-warning' : 'alert-info'; ?> mt-4">
                <h5 class="alert-heading">Impatto sul Budget</h5>
                <p><strong>Budget Attuale:</strong> <?= htmlspecialchars(number_format($budget_progetto, 2)); ?>€</p>
                <p><strong>Costo Totale dei Componenti (dopo inserimento):</strong> <?= htmlspecialchars(number_format($nuovo_costo_totale, 2)); ?>€</p>
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
            <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto]); ?>"
               class="btn btn-secondary">Annulla</a>
            <form action="<?=generate_url('componente_insert') ?>" method="post">
                <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($nome_progetto); ?>">
                <input type="hidden" name="nome_componente" value="<?= htmlspecialchars($nome_componente); ?>">
                <input type="hidden" name="descrizione" value="<?= htmlspecialchars($descrizione); ?>">
                <input type="hidden" name="quantita" value="<?= htmlspecialchars($quantita); ?>">
                <input type="hidden" name="prezzo" value="<?= htmlspecialchars($prezzo); ?>">
                <button type="submit" class="btn btn-primary">Conferma Inserimento</button>
            </form>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>