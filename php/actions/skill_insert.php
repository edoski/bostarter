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
    ['competenza'],
    '../public/curriculum.php'
);

// === ACTION ===
// Inserimento della skill nella lista globale della piattaforma
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
        '../public/curriculum.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill (globale) aggiunta con successo.',
    '../public/curriculum.php'
);