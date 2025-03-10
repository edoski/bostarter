<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è un amministratore
checkAdmin();

// 3. Sono stati forniti tutti i parametri necessari
if (!isset($_POST['vecchia_competenza']) || !isset($_POST['nuova_competenza'])) {
    redirect(
        false,
        "Parametri mancanti. Riprova.",
        "../public/curriculum.php"
    );
}

// 4. La nuova competenza non è vuota
if (empty(trim($_POST['nuova_competenza']))) {
    redirect(
        false,
        "Il nome della nuova competenza non può essere vuoto.",
        "../public/curriculum.php"
    );
}

// === ACTION ===
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
    'Skill globale aggiornata correttamente.',
    '../public/curriculum.php'
);