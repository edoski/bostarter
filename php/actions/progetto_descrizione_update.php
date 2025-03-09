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
if (!($_SESSION['is_creatore'] && checkProgettoOwner($_SESSION['email'], $_POST['nome']))) {
    redirect(
        false,
        "Non sei autorizzato ad effettuare questa operazione.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// 4. Controllo che l'attributo sia stato specificato
if (!isset($_POST['attr'])) {
    redirect(
        false,
        "Attributo non specificato. Riprova.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// 5. Controllo che l'attributo sia valido
$attr = $_POST['attr'];
if ($attr !== 'descrizione' && $attr !== 'foto') {
    redirect(
        false,
        "Attributo non valido. Riprova.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

// === ACTION ===
if ($attr === 'descrizione') {
    // Controllo che la descrizione sia stata specificata e non vuota
    if (!isset($_POST['descrizione']) || empty(trim($_POST['descrizione']))) {
        redirect(
            false,
            "Descrizione non specificata. Riprova.",
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }

    try {
        $in = [
            'p_nome'           => $_POST['nome'],
            'p_email_creatore' => $_SESSION['email'],
            'p_descrizione'    => $_POST['descrizione']
        ];
        sp_invoke('sp_progetto_descrizione_update', $in);
    } catch (PDOException $e) {
        redirect(
            false,
            "Errore durante l'aggiornamento della descrizione: " . $e->errorInfo[2],
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }

    // Success, redirect alla pagina del progetto
    redirect(
        true,
        "Descrizione aggiornata correttamente.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}

if ($attr === 'foto') {
    // Controllo che la foto sia stata specificata e sia stata caricata correttamente
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        redirect(
            false,
            "Foto non specificata o errore nel caricamento. Riprova.",
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }

    // Controllo che il file sia un'immagine valida
    $imageData = file_get_contents($_FILES['foto']['tmp_name']);
    if ($imageData === false) {
        redirect(
            false,
            "Errore durante la lettura del file immagine. Riprova.",
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }
    try {
        $in = [
            'p_nome_progetto'  => $_POST['nome'],
            'p_email_creatore' => $_SESSION['email'],
            'p_foto'           => $imageData
        ];
        sp_invoke('sp_foto_insert', $in);
    } catch (PDOException $e) {
        redirect(
            false,
            "Errore durante l'aggiornamento dell'immagine: " . $e->errorInfo[2],
            "../public/progetto_dettagli.php?nome=" . $_POST['nome']
        );
    }

    // Success, redirect alla pagina del progetto
    redirect(
        true,
        "Foto inserita correttamente.",
        "../public/progetto_dettagli.php?nome=" . $_POST['nome']
    );
}