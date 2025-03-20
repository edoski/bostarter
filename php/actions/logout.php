<?php
/**
 * ACTION: logout
 * PERFORMED BY: ALL
 * UI: components/header.php
 *
 * PURPOSE:
 * - Gestisce il logout di un utente.
 * - Distrugge la sessione e cancella il cookie di sessione.
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - nickname: Nickname dell'utente
 * - is_admin: Flag che indica se l'utente è un amministratore
 * - is_creatore: Flag che indica se l'utente è un creatore
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];
$nickname = $_SESSION['nickname'];
$is_admin = $_SESSION['is_admin'];
$is_creatore = $_SESSION['is_creatore'];

// === CONTEXT ===
$context = [
    'collection' => 'UTENTE',
    'action' => 'LOGOUT',
    'email' => $email,
    'redirect' => generate_url('login')
];
$pipeline = new EventPipeline($context);

// === ACTION ===
// SERVER-SIDE: DISTRUGGO LA SESSIONE
session_unset();
session_destroy();

// CLIENT-SIDE: CANCELLO IL COOKIE DI SESSIONE
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// === SUCCESS ===
// DATI DI LOGGING
$logs = [
    'email' => $email,
    'nickname' => $nickname,
    'is_admin' => $is_admin,
    'is_creatore' => $is_creatore
];

// REDIRECT ALLA PAGINA DI LOGIN
$pipeline->continue(null, $logs);