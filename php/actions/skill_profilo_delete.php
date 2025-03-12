<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_profilo', 'competenza']);

$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];

// 3. L'utente è il creatore del progetto
checkProgettoOwner($nome_progetto);

// === ACTION ===
// Rimozione della skill dal profilo del progetto
try {
    $in = [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_competenza' => $competenza,
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_skill_profilo_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la rimozione della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
    );
}

// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Competenza rimossa dal profilo con successo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
);