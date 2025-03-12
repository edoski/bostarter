<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome', 'budget', 'tipo']);

$nome_progetto = $_POST['nome'];
$budget = floatval($_POST['budget']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($nome_progetto);

// 4. Se il progetto è chiuso, non è possibile modificare il budget
checkProgettoAperto($nome_progetto);

// 5. Il budget è un numero positivo
if ($budget <= 0) {
    redirect(
        false,
        "Il budget deve essere un numero positivo.",
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// 6. Se il progetto è di tipo HARDWARE, controllo che il nuovo budget >= costo delle componenti
if ($_POST['tipo'] === 'HARDWARE') {
    try {
        $in = ['p_nome_progetto' => $nome_progetto];
        $out = ['p_costo_totale_out' => 0];
        sp_invoke('sp_util_progetto_componenti_costo', $in, $out)[0]['p_costo_totale_out'] ?? 0;

        if ($out['p_costo_totale_out'] > $budget) {
            redirect(
                false,
                "Il budget deve essere maggiore o uguale al costo delle sue componenti.",
                "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
            );
        }
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del costo delle componenti: " . $ex->errorInfo[2],
            '../public/progetti.php'
        );
    }
}

// === ACTION ===
// Aggiornamento del budget
try {
    $in = [
        'p_nome' => $nome_progetto,
        'p_email_creatore' => $_SESSION['email'],
        'p_budget' => $budget
    ];

    sp_invoke('sp_progetto_budget_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiornamento del budget: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Budget aggiornato con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
);