<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è un creatore
if (!$_SESSION['is_creatore']) {
    redirect(
        false,
        "Solo i creatori possono rimuovere competenze da un profilo.",
        "../public/progetti.php"
    );
}

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) || !isset($_POST['competenza'])) {
    redirect(
        false,
        "Dati mancanti per la rimozione della competenza.",
        "../public/progetti.php"
    );
}

// 4. L'utente è il creatore del progetto
if (!isProgettoOwner($_SESSION['email'], $_POST['nome_progetto'])) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
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
    // Memorizza i dati nella sessione per riutilizzarli dopo il redirect
    $_SESSION['temp_profilo_nome'] = $_POST['nome_profilo'];
    $_SESSION['temp_profilo_update'] = true;

    redirect(
        false,
        "Errore durante la rimozione della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Memorizza i dati nella sessione per riutilizzarli dopo il redirect
$_SESSION['temp_profilo_nome'] = $_POST['nome_profilo'];
$_SESSION['temp_profilo_update'] = true;

// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Competenza rimossa con successo dal profilo.",
    "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
);