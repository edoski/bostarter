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
        "Solo i creatori possono inserire profili.",
        "../public/progetti.php"
    );
}

// 3. Parametri necessari sono stati forniti
if (!isset($_POST['nome_progetto']) || !isset($_POST['nome_profilo']) ||
    !isset($_POST['competenze']) || !isset($_POST['livelli'])) {
    redirect(
        false,
        "Dati mancanti per l'inserimento del profilo.",
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

// === ACTION ===
// Prima inserisco il profilo
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
        "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Inserisco le competenze associate al profilo
for ($i = 0; $i < count($_POST['competenze']); $i++) {
    $competenza = $_POST['competenze'][$i];
    $livello = intval($_POST['livelli'][$i]);

    // Validazione dei dati
    if (empty($competenza) || $livello < 0 || $livello > 5) {
        continue; // Salta questa competenza se non valida
    }

    try {
        $in = [
            'p_nome_profilo' => $_POST['nome_profilo'],
            'p_nome_progetto' => $_POST['nome_progetto'],
            'p_email_creatore' => $_SESSION['email'],
            'p_competenza' => $competenza,
            'p_livello_richiesto' => $livello
        ];

        sp_invoke('sp_skill_profilo_insert', $in);
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante l'inserimento della competenza: " . $ex->errorInfo[2],
            "../public/progetto_aggiorna.php?attr=profilo&nome=" . urlencode($_POST['nome_progetto'])
        );
    }
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Profilo inserito con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);