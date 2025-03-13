<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome', 'attr']);

$attr = $_POST['attr'];
$nome_progetto = $_POST['nome'];
$email_creatore = $_SESSION['email'];

// 3. L'utente è il creatore del progetto
checkProgettoOwner($nome_progetto);

// 4. Controllo che l'attributo sia valido
if ($attr !== 'descrizione' && $attr !== 'foto') {
    redirect(
        false,
        "Attributo non valido. Riprova.",
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// === ACTION ===
if ($attr === 'descrizione') {
    // Controllo che la descrizione sia stata specificata
    checkSetVars(
        ['descrizione'],
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );

    try {
        $in = [
            'p_nome'           => $nome_progetto,
            'p_email_creatore' => $email_creatore,
            'p_descrizione'    => $_POST['descrizione']
        ];
        sp_invoke('sp_progetto_descrizione_update', $in);
    } catch (PDOException $e) {
        redirect(
            false,
            "Errore durante l'aggiornamento della descrizione: " . $e->errorInfo[2],
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }

    // Success, redirect alla pagina del progetto
    redirect(
        true,
        "Descrizione aggiornata con successo.",
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

if ($attr === 'foto') {
    // Controllo che la foto sia stata specificata e sia stata caricata correttamente
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != UPLOAD_ERR_OK) {
        redirect(
            false,
            "Foto non specificata o errore nel caricamento. Riprova.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }

    // Controllo che il file sia un'immagine valida
    $imageData = file_get_contents($_FILES['foto']['tmp_name']);
    if ($imageData === false) {
        redirect(
            false,
            "Errore durante la lettura del file immagine. Riprova.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }
    try {
        $in = [
            'p_nome_progetto'  => $nome_progetto,
            'p_email_creatore' => $email_creatore,
            'p_foto'           => $imageData
        ];
        sp_invoke('sp_foto_insert', $in);
    } catch (PDOException $e) {
        redirect(
            false,
            "Errore durante l'aggiornamento dell'immagine: " . $e->errorInfo[2],
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }

    // Success, redirect alla pagina del progetto
    redirect(
        true,
        "Foto inserita con successo.",
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}