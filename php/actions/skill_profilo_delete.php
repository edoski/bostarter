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
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) || !isset($_POST['competenza'])) {
    redirect(
        false,
        "Dati mancanti per la rimozione della competenza.",
        "../public/progetti.php"
    );
}

// === ACTION ===
try {
    $in = [
        'p_nome_profilo' => $_POST['nome_profilo'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_competenza' => $_POST['competenza'],
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_skill_profilo_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la rimozione della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Competenza rimossa correttamente dal profilo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
);