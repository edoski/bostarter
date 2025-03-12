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
// Elimino il profilo (e tutte le skill associate ad esso + le candidature)
try {
    $in = [
        'p_nome_profilo' => $_POST['nome_profilo'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_profilo_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'eliminazione del profilo: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
    );
}
// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Profilo '" . $_POST['nome_profilo'] . "' eliminato con successo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto'])
);