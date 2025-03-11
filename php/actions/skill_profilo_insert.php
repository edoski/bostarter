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
        "Solo i creatori possono aggiungere competenze ad un profilo.",
        "../public/progetti.php"
    );
}

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) ||
    !isset($_POST['competenza']) || !isset($_POST['livello'])) {
    redirect(
        false,
        "Dati mancanti per l'aggiunta della competenza.",
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

// 5. Il livello è valido
$livello = intval($_POST['livello']);
if ($livello < 0 || $livello > 5) {
    redirect(
        false,
        "Il livello deve essere compreso tra 0 e 5.",
        "../public/progetti.php"
    );
}

// === ACTION ===
try {
    $in = [
        'p_nome_profilo' => $_POST['nome_profilo'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_email_creatore' => $_SESSION['email'],
        'p_competenza' => $_POST['competenza'],
        'p_livello_richiesto' => $livello
    ];

    sp_invoke('sp_skill_profilo_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiunta della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
    );
}

// Success, redirect alla pagina del profilo
redirect(
    true,
    "Competenza aggiunta con successo al profilo.",
    "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
);