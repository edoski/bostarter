<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// Recupero e pulisco i dati inviati dal form
$email = trim($_POST['email'] ?? '');
$nickname = trim($_POST['nickname'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$cognome = trim($_POST['cognome'] ?? '');
$anno_nascita = trim($_POST['anno_nascita'] ?? '');
$luogo_nascita = trim($_POST['luogo_nascita'] ?? '');
$password = trim($_POST['password'] ?? '');
$conferma_password = trim($_POST['conferma_password'] ?? '');
$is_creatore = isset($_POST['is_creatore']) ? 1 : 0;
$is_admin = isset($_POST['is_admin']) ? 1 : 0;
$codice_sicurezza = trim($_POST['codice_sicurezza'] ?? '');

// Controllo che l'utente sia maggiorenne
if ($anno_nascita > date('Y') - 18) {
    redirect(
        false,
        "Devi essere maggiorenne per registrarti.",
        '../public/register.php'
    );
}

// Controllo che le password siano uguali
if ($password !== $conferma_password) {
    redirect(
        false,
        "Le password non corrispondono.",
        '../public/register.php'
    );
}

// Controllo che il codice di sicurezza sia definito se l'utente si registra come amministratore
if ($is_admin && (empty($codice_sicurezza) || strlen($codice_sicurezza) < 8)) {
    redirect(
        false,
        "Devi inserire un codice di sicurezza valido per registrarti come amministratore.",
        '../public/register.php'
    );
}

// === ACTION ===
// Registrazione dell'utente
try {
    $in = [
        'p_email' => $email,
        'p_password' => password_hash($password, PASSWORD_DEFAULT), // Memorizzo hashed pwd, non plaintext
        'p_nickname' => $nickname,
        'p_nome' => $nome,
        'p_cognome' => $cognome,
        'p_anno_nascita' => $anno_nascita,
        'p_luogo_nascita' => $luogo_nascita,
        'p_is_creatore' => $is_creatore,
        'p_is_admin' => $is_admin,
        'p_codice_sicurezza' => password_hash($codice_sicurezza, PASSWORD_DEFAULT) // Memorizzo hashed codice_sicurezza, non plaintext
    ];

    sp_invoke('sp_utente_register', $in);
} catch (PDOException $e) {
    redirect(
        false,
        "Errore durante la registrazione. Riprova." . "\n" . $e->getMessage(),
        '../public/register.php'
    );
}

// Success, redirect alla home con variabili di sessione
$_SESSION['email'] = $email;
$_SESSION['nickname'] = $nickname;
$_SESSION['nome'] = $nome;
$_SESSION['cognome'] = $cognome;
$_SESSION['anno_nascita'] = $anno_nascita;
$_SESSION['luogo_nascita'] = $luogo_nascita;
$_SESSION['is_creatore'] = $is_creatore;
$_SESSION['is_admin'] = $is_admin;

redirect(
    true,
    "Registrazione effettuata correttamente.",
    '../public/home.php'
);