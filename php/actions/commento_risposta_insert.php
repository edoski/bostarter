<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
// 2. Sono stati inviati tutti i dati necessari
checkAuth();
check_CommentoRisposta_validComment();

// 3. L'utente sia il creatore del progetto
if (!checkProgettoOwner($_SESSION['email'], $_POST['nome_progetto'])) {
    redirect(
        false,
        "Non sei il creatore di questo progetto.",
        "../public/progetti.php"
    );
}

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
    "Risposta inviata correttamente.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);