<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
// 2. È stato selezionato un progetto valido
// 3. È stato selezionato un commento valido
checkAuth();
checkProgettoSelected();
check_Commento_validInsert();

// === ACTION ===
// Inserisco il commento
try {
    $in = [
        'p_email_utente' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto'],
        'p_commento' => $_POST['commento']
    ];

    sp_invoke('sp_commento_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'inserimento del commento: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Commento inserito correttamente.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);