<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_componente']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];

// === ACTION ===
// Eliminazione del componente dal progetto
try {
    $in = [
        'p_nome_componente' => $nome_componente,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_componente_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'eliminazione del componente: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($nome_progetto)
    );
}

// Success, redirect alla pagina di gestione componenti
redirect(
    true,
    "Componente eliminato con successo.",
    "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($nome_progetto)
);