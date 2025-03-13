<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['id_commento', 'nome_progetto', 'risposta']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// === ACTION ===
// Se tutti i controlli passano, invio la risposta
try {
    $in = [
        'p_commento_id' => $_POST['id_commento'],
        'p_email_creatore' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_risposta' => $_POST['risposta']
    ];

    sp_invoke('sp_commento_risposta_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'inserimento della risposta: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Risposta inviata con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);