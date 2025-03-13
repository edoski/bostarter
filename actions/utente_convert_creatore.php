<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente non è un creatore
if ($_SESSION['is_creatore']) {
    redirect(
        false,
        "Sei già un utente creatore.",
        "../public/progetti.php"
    );
}

// === ACTION ===
// Aggiornamento del ruolo dell'utente a creatore
try {
    $in = ['p_email' => $_SESSION['email']];
    sp_invoke('sp_util_utente_convert_creatore', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la conversione dell'utente a creatore: " . $ex->errorInfo[2],
        "../public/progetti.php"
    );
}

// Success, redirect alla pagina dei progetti
$_SESSION['is_creatore'] = true;
redirect(
    true,
    "Complimenti! Ora sei un utente creatore.",
    "../public/home.php"
);