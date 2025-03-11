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
        "Solo i creatori possono aggiornare le competenze di un profilo.",
        "../public/progetti.php"
    );
}

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) ||
    !isset($_POST['competenza']) || !isset($_POST['nuovo_livello'])) {
    redirect(
        false,
        "Dati mancanti per l'aggiornamento della competenza.",
        "../public/progetti.php"
    );
}

// 4. L'utente è il creatore del progetto
if (!checkProgettoOwner($_SESSION['email'], $_POST['nome_progetto'])) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// 5. Il nuovo livello è valido
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
    // Memorizza i dati nella sessione per riutilizzarli dopo il redirect
    $_SESSION['temp_profilo_nome'] = $_POST['nome_profilo'];
    $_SESSION['temp_profilo_update'] = true;

    redirect(
        false,
        "Errore durante l'aggiornamento della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Memorizza i dati nella sessione per riutilizzarli dopo il redirect
$_SESSION['temp_profilo_nome'] = $_POST['nome_profilo'];
$_SESSION['temp_profilo_update'] = true;

// Success, redirect alla pagina di gestione profili
redirect(
    true,
    "Livello della competenza aggiornato con successo.",
    "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
);