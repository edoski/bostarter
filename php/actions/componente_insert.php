<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_componente', 'descrizione', 'quantita', 'prezzo']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome_progetto']);

$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);

// === ACTION ===
// Inserimento del componente nel progetto
try {
    $in = [
        'p_nome_componente' => $nome_componente,
        'p_nome_progetto' => $nome_progetto,
        'p_descrizione' => $descrizione,
        'p_quantita' => $quantita,
        'p_prezzo' => $prezzo,
        'p_email_creatore' => $_SESSION['email']
    ];

    sp_invoke('sp_componente_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'inserimento del componente: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($nome_progetto)
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Componente inserito con successo.",
    "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
);