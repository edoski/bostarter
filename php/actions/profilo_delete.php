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
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo'])) {
    redirect(
        false,
        "Dati mancanti per l'eliminazione del profilo.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// === ACTION ===
// Elimino il profilo (la stored procedure si occuperà di eliminare anche le skill associate e le candidature)
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
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
    );
}
// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Profilo '" . $_POST['nome_profilo'] . "' eliminato correttamente.",
    "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
);