<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome', 'id']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome']);

// === ACTION ===
// Eliminazione della foto
try {
    $in = [
        'p_nome_progetto' => $_POST['nome'],
        'p_email_creatore' => $_SESSION['email'],
        'p_foto_id' => $_POST['id']
    ];

    sp_invoke('sp_foto_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'eliminazione della foto: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// Success, redirect alla pagina di modifica del progetto
redirect(
    true,
    "Foto eliminata con successo.",
    "../public/progetto_aggiorna.php?nome=" . $_POST['nome'] . "&attr=descrizione"
);