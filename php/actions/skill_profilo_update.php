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
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) ||
    !isset($_POST['competenza']) || !isset($_POST['nuovo_livello'])) {
    redirect(
        false,
        "Dati mancanti per l'aggiornamento della competenza.",
        "../public/progetti.php"
    );
}

// 4. Il nuovo livello è valido
$nuovoLivello = intval($_POST['nuovo_livello']);
if ($nuovoLivello < 0 || $nuovoLivello > 5) {
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
        'p_competenza' => $_POST['competenza'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_email_creatore' => $_SESSION['email'],
        'p_nuovo_livello_richiesto' => $nuovoLivello
    ];

    sp_invoke('sp_skill_profilo_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiornamento della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
    );
}

// Success, redirect alla pagina del profilo
redirect(
    true,
    "Livello della competenza aggiornato correttamente.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_POST['nome_progetto']) . "&profilo=" . urlencode($_POST['nome_profilo'])
);