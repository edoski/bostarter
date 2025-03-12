<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(
    ['competenza', 'livello'],
    '../public/curriculum.php'
);

// === ACTION ===
// Inserimento della skill nel curriculum dell'utente
try {
    $in = [
        'p_email' => $_SESSION['email'],
        'p_competenza' => $_POST['competenza'],
        'p_livello' => $_POST['livello']
    ];

    sp_invoke('sp_skill_curriculum_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'inserimento della skill: " . $ex->errorInfo[2],
        '../public/curriculum.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill aggiunta con successo.',
    '../public/curriculum.php'
);