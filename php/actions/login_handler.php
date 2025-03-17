<?php
/**
 * ACTION: login_handler
 * PERFORMED BY: ALL
 * UI: public/login.php
 *
 * PURPOSE:
 * - Gestisce l'autenticazione di un utente.
 * - Verifica le credenziali e, se corrette, imposta le variabili di sessione.
 * - Se l'utente è un admin, richiede anche il codice di sicurezza.
 * - Per maggiori dettagli, vedere la documentazione delle stored procedure: "sp_utente_login", "sp_utente_select"
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - password: Password dell'utente
 * - codice_sicurezza: Codice di sicurezza (solo per admin)
 */

// === SETUP ===
session_start();
require '../config/config.php';

// === VARIABLES ===
check_POST(['email', 'password']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$codice_sicurezza = $_POST['codice_sicurezza'] ?? null; // Se non è un admin, il codice di sicurezza è null

// === CONTEXT ===
$context = [
    'collection' => 'UTENTE',
    'action' => 'LOGIN',
    'redirect_fail' => generate_url('login'),
    'redirect_success' => generate_url('home'),
    'procedure' => 'sp_utente_login',
    'in' => ['p_email' => $email]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'EMAIL È VALIDA
$pipeline->check(
    !filter_var($email, FILTER_VALIDATE_EMAIL),
    "L'indirizzo email inserito non è valido. Riprova."
);

// L'UTENTE CON L'EMAIL SPECIFICATA ESISTE
$login_data = $pipeline->fetch($context['procedure']);

// LA PASSWORD INSERITA È CORRETTA
$pipeline->check(
    !password_verify($password, $login_data['password']),
    "Password inserita non valida. Riprova."
);

// CONTROLLO SE L'UTENTE È UN ADMIN
$is_admin = $pipeline->fetch('sp_util_admin_exists')['is_admin'];

// SE L'UTENTE È UN ADMIN, CONTROLLO IL CODICE DI SICUREZZA
if ($is_admin) {
    $codice_out = $pipeline->fetch('sp_util_admin_get_codice_sicurezza')['codice_sicurezza'];
    $pipeline->check(
        !password_verify($codice_sicurezza, $codice_out),
        "Codice di sicurezza non valido. Riprova."
    );
}

// CONTROLLO SE L'UTENTE È UN CREATORE
$is_creatore = $pipeline->fetch('sp_util_creatore_exists')['is_creatore'];

// === ACTION ===
// RECUPERO I DATI DELL'UTENTE
$user_data = $pipeline->fetch('sp_utente_select');

// === SUCCESS ===
// IMPOSTO LE VARIABILI DI SESSIONE
$_SESSION['email'] = htmlspecialchars($login_data['email']);
$_SESSION['nickname'] = htmlspecialchars($login_data['nickname']);
$_SESSION['nome'] = htmlspecialchars($user_data['nome']);
$_SESSION['cognome'] = htmlspecialchars($user_data['cognome']);
$_SESSION['luogo_nascita'] = htmlspecialchars($user_data['luogo_nascita']);
$_SESSION['anno_nascita'] = htmlspecialchars($user_data['anno_nascita']);
$_SESSION['is_admin'] = $is_admin;
$_SESSION['is_creatore'] = $is_creatore;

// DATI DA LOGGARE
$logs = [
    'p_email' => $email,
    'p_nickname' => $_SESSION['nickname'],
    'p_is_admin' => $_SESSION['is_admin'],
    'p_is_creatore' => $_SESSION['is_creatore']
];

// REDIRECT ALLA PAGINA HOME
$pipeline->continue("Login effettuato con successo.", $logs);