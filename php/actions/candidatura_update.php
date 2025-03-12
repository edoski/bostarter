<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

// 3. Le variabili POST sono state impostate correttamente
checkSetVars(
    ['email_candidato', 'nome_progetto', 'nome_profilo', 'nuovo_stato'],
    "../public/candidature.php"
);

// 4. Il nuovo stato è valido
if ($_POST['nuovo_stato'] != 'accettato' && $_POST['nuovo_stato'] != 'rifiutato') {
    redirect(
        false,
        "Stato non valido. Riprova.",
        "../public/candidature.php"
    );
}

// === ACTION ===
try {
    $in = [
        'p_email_creatore' => $_SESSION['email'],
        'p_email_candidato' => $_POST['email_candidato'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_nome_profilo' => $_POST['nome_profilo'],
        'p_nuovo_stato' => $_POST['nuovo_stato']
    ];

    sp_invoke('sp_partecipante_creatore_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiornamento della candidatura: " . $ex->errorInfo[2],
        "../public/candidature.php"
    );
}

// Success, redirect alla pagina delle candidature
redirect(
    true,
    "Candidatura " . ($_POST['nuovo_stato'] == 'accettato' ? "accettata" : "rifiutata") . " con successo.",
    "../public/candidature.php"
);