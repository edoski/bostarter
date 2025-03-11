<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del progetto
if (!($_SESSION['is_creatore'] && isProgettoOwner($_SESSION['email'], $_POST['nome_progetto']))) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetti.php"
    );
}

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo'])) {
    redirect(
        false,
        "Dati mancanti per l'inserimento del profilo.",
        "../public/progetti.php"
    );
}

// === ACTION ===
// Inserisco il profilo
try {
    $in = [
        'p_nome_profilo' => $_POST['nome_profilo'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_profilo_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'inserimento del profilo: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina di modifica del profilo appena creato
redirect(
    true,
    "Profilo creato con successo. Aggiungi ora le competenze necessarie.",
    "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
);