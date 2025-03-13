<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è un amministratore
checkAdmin();

// 3. Le variabili POST sono state impostate correttamente
checkSetVars(
    ['vecchia_competenza', 'nuova_competenza'],
    "../public/curriculum.php"
);

// === ACTION ===
// Aggiornamento del nome della skill globale della piattaforma
try {
    $in = [
        'p_email_admin' => $_SESSION['email'],
        'p_vecchia_competenza' => $_POST['vecchia_competenza'],
        'p_nuova_competenza' => $_POST['nuova_competenza']
    ];

    sp_invoke('sp_skill_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'aggiornamento della skill globale: " . $ex->errorInfo[2],
        '../public/curriculum.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill globale aggiornata con successo.',
    '../public/curriculum.php'
);