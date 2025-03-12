<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'commento']);

// 3. Controllo che il commento sia sufficientemente lungo
if (strlen(trim($_POST['commento'])) < 1) {
    redirect(
        false,
        "Il commento deve essere lungo almeno 1 carattere.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// === ACTION ===
// Inserisco il commento
try {
    $in = [
        'p_email_utente' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_commento' => $_POST['commento']
    ];

    sp_invoke('sp_commento_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'inserimento del commento: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Commento inserito con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);