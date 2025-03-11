<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Controllo che il progetto sia stato specificato
if (!isset($_POST['nome'])) {
    redirect(
        false,
        "Progetto non specificato. Riprova.",
        "../public/progetti.php"
    );
}

// 3. L'utente è il creatore del progetto
if (!($_SESSION['is_creatore'] && isProgettoOwner($_SESSION['email'], $_POST['nome']))) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// 4. La foto è stata specificata
if (!isset($_POST['id'])) {
    redirect(
        false,
        "Foto non specificata. Riprova.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// === ACTION ===
// Elimino la foto
try {
    $in = [
        'p_nome_progetto' => $_POST['nome'],
        'p_email_creatore' => $_SESSION['email'],
        'p_foto_id' => $_POST['id']
    ];

    sp_invoke('sp_foto_delete', $in);
} catch (PDOException $e) {
    redirect(
        false,
        "Errore durante l'eliminazione della foto: " . $e->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// Success, redirect alla pagina di modifica del progetto
redirect(
    true,
    "Foto eliminata con successo.",
    "../public/progetto_aggiorna.php?nome=" . $_POST['nome'] . "&attr=descrizione"
);