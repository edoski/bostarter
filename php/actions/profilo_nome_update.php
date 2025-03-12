<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['profilo']) || !isset($_POST['nuovo_nome'])) {
    redirect(
        false,
        "Dati mancanti per l'aggiornamento del nome del profilo.",
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// === ACTION ===
try {
    $in = [
        'p_nome_profilo' => $_POST['profilo'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_nuovo_nome' => $_POST['nuovo_nome'],
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_profilo_nome_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiornamento del nome del profilo: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del profilo
redirect(
    true,
    "Nome del profilo aggiornato con successo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nuovo_nome'])
);