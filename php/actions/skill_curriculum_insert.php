<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// Controllo se l'utente ha effettuato il login
checkAuth();

// === ACTION ===
// Inserisco la skill associata all'utente
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
        '../public/skill.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill aggiunta correttamente.',
    '../public/skill.php'
);