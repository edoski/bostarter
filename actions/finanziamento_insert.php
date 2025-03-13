<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. Controllo che l'utente sia autenticato
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome', 'importo', 'reward']);

$nome_progetto = $_POST['nome'];
$importo = floatval($_POST['importo']);
$reward = $_POST['reward'];
$email = $_SESSION['email'];

// 3. Controllo che la reward selezionata sia valida per l'importo donato
try {
    $in = ['p_nome_progetto' => $nome_progetto, 'p_importo' => $importo];
    $valid_rewards = sp_invoke('sp_reward_selectAllByFinanziamentoImporto', $in);

    $reward_valid = false;
    foreach ($valid_rewards as $r) {
        if ($r['codice'] == $reward) {
            $reward_valid = true;
            break;
        }
    }

    if (!$reward_valid) {
        redirect(
            false,
            "Reward selezionata non valida per l'importo donato.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la validazione della reward: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// === ACTION ===
// Inserimento del finanziamento per il progetto
try {
    $in = [
        'p_email' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_codice_reward' => $reward,
        'p_importo' => $importo
    ];

    sp_invoke('sp_finanziamento_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'inserimento del finanziamento: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Finanziamento completato con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
);