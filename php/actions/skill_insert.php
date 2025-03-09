<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
// 2. L'utente è un amministratore
checkAuth();
checkAdmin();

// === ACTION ===
// Inserisco la skill associata all'utente
try {
    $in = [
        'p_competenza' => $_POST['competenza'],
        'p_email' => $_SESSION['email']
    ];

    sp_invoke('sp_skill_insert', $in);
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
    'Skill (globale) aggiunta correttamente.',
    '../public/skill.php'
);