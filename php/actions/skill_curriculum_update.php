<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Sono stati forniti tutti i parametri necessari
if (!isset($_POST['competenza']) || !isset($_POST['livello'])) {
    redirect(
        false,
        "Parametri mancanti. Riprova.",
        "../public/curriculum.php"
    );
}

// 3. Il livello è valido (0-5)
$livello = intval($_POST['livello']);
if ($livello < 0 || $livello > 5) {
    redirect(
        false,
        "Livello non valido. Il livello deve essere compreso tra 0 e 5.",
        "../public/curriculum.php"
    );
}

// === ACTION ===
try {
    $in = [
        'p_email' => $_SESSION['email'],
        'p_competenza' => $_POST['competenza'],
        'p_livello' => $livello
    ];

    sp_invoke('sp_skill_curriculum_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'aggiornamento della skill: " . $ex->errorInfo[2],
        '../public/curriculum.php'
    );
}

// Success, redirect alla pagina delle skill
redirect(
    true,
    'Skill aggiornata correttamente.',
    '../public/curriculum.php'
);