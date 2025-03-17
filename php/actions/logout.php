<?php
/**
 * ACTION: logout
 * PERFORMED BY: ALL
 * UI: components/header.php
 *
 * PURPOSE:
 * - Gestisce il logout di un utente.
 * - Distrugge la sessione e cancella il cookie di sessione.
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === CONTEXT ===
$context = [
    'collection' => 'UTENTE',
    'action' => 'LOGOUT',
    'redirect' => generate_url('login')
];
$pipeline = new ValidationPipeline($context);

// === ACTION ===
// DATI DI LOGGING
$logs = [
    'email' => $_SESSION['email'],
    'nickname' => $_SESSION['nickname'],
    'is_admin' => $_SESSION['is_admin'],
    'is_creatore' => $_SESSION['is_creatore']
];

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
// REDIRECT ALLA PAGINA DI LOGIN
$pipeline->continue("Logout effettuato con successo.", $logs);