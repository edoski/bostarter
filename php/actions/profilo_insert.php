<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_profilo']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// === ACTION ===
// Inserimento del profilo
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
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina di modifica del profilo appena creato
redirect(
    true,
    "Profilo creato con successo. Aggiungi ora le competenze necessarie.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
);