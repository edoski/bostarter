<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente sia il creatore del progetto, oppure è un admin
if (!$_SESSION['is_admin']) {
    checkProgettoOwner($_POST['nome_progetto']);
}

// 3. Sono stati inviati tutti i dati necessari
if (!isset($_POST['id_commento']) || !isset($_POST['nome_progetto'])) {
    redirect(
        false,
        "Errore durante il controllo del commento. Riprova.",
        "../public/progetti.php"
    );
}

// === ACTION ===
// Se tutti i controlli passano, cancello la risposta
try {
    $in = [
        'p_commento_id' => $_POST['id_commento'],
        'p_email_creatore' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto'],

    ];

    sp_invoke('sp_commento_risposta_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la cancellazione della risposta: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Risposta cancellata correttamente.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);