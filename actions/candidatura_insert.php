<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_profilo']);

// === ACTION ===
try {
    $in = [
        'p_email' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_nome_profilo' => $_POST['nome_profilo']
    ];

    sp_invoke('sp_partecipante_utente_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'invio della candidatura: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Candidatura inviata con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);