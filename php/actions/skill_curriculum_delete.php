<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(
    ['competenza'],
    '../public/curriculum.php'
);

// === ACTION ===
// Rimozione della skill dal curriculum dell'utente
try {
    $in = [
        'p_email' => $_SESSION['email'],
        'p_competenza' => $_POST['competenza']
    ];

    sp_invoke('sp_skill_curriculum_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nella rimozione della skill: " . $ex->errorInfo[2],
        '../public/curriculum.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill rimossa dal curriculum con successo.',
    '../public/curriculum.php'
);