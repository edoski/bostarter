<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del progetto
checkProgettoOwner($_POST['nome']);

// 3. I dati sono stati inviati correttamente
if (!isset($_POST['nome']) || !isset($_POST['codice']) || !isset($_POST['descrizione']) || !isset($_POST['min_importo']) || !isset($_FILES['foto'])) {
    redirect(
        false,
        "Dati reward mancanti. Riprova.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome'])
    );
}

$nome_progetto = $_POST['nome'];
$codice = $_POST['codice'];
$descrizione = $_POST['descrizione'];
$min_importo = floatval($_POST['min_importo']);

// 4. Il min_importo è un numero positivo
if ($min_importo <= 0) {
    redirect(
        false,
        "L'importo minimo deve essere un numero positivo",
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// 5. La foto è un'immagine valida
// Controllo che la foto sia stata caricata correttamente
if (!$_FILES['foto']['error'] != UPLOAD_ERR_OK) {
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


// === ACTION ===
try {
    $in = [
        'p_codice' => $codice,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $_SESSION['email'],
        'p_descrizione' => $descrizione,
        'p_foto' => $imageData,
        'p_min_importo' => $min_importo
    ];

    sp_invoke('sp_reward_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'inserimento della reward: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Reward inserita correttamente.",
    "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
);