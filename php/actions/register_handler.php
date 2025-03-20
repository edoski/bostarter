<?php
/**
 * ACTION: register_handler
 * PERFORMED BY: ALL
 * UI: public/register.php
 *
 * PURPOSE:
 * - Registra un nuovo utente nella piattaforma.
 * - L'utente può registrarsi come creatore e/o amministratore.
 * - Se l'operazione va a buon fine, l'utente viene inserito nella tabella "UTENTE" e opzionalmente nelle tabelle "CREATORE" e/o "ADMIN".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_utente_register".
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - password: Password dell'utente
 * - nickname: Nickname dell'utente
 * - nome: Nome dell'utente
 * - cognome: Cognome dell'utente
 * - anno_nascita: Anno di nascita dell'utente
 * - luogo_nascita: Luogo di nascita dell'utente
 * - is_creatore: Flag che indica se l'utente è un creatore
 * - is_admin: Flag che indica se l'utente è un amministratore
 * - codice_sicurezza: Codice di sicurezza (solo per amministratori)
 */

// === SETUP ===
session_start();
require '../config/config.php';

// === VARIABLES ===
check_POST(['email', 'nickname', 'nome', 'cognome', 'anno_nascita', 'luogo_nascita', 'password', 'conferma_password']);
$email = htmlspecialchars(trim($_POST['email']));
$nickname = htmlspecialchars(trim($_POST['nickname']));
$nome = htmlspecialchars(trim($_POST['nome']));
$cognome = htmlspecialchars(trim($_POST['cognome']));
$anno_nascita = htmlspecialchars(trim($_POST['anno_nascita']));
$luogo_nascita = htmlspecialchars(trim($_POST['luogo_nascita']));
$password = trim($_POST['password']);
$conferma_password = trim($_POST['conferma_password']);
$is_creatore = isset($_POST['is_creatore']) ? 1 : 0;
$is_admin = isset($_POST['is_admin']) ? 1 : 0;
$codice_sicurezza = trim($_POST['codice_sicurezza'] ?? '');

// === CONTEXT ===
$context = [
    'collection' => 'UTENTE',
    'action' => 'REGISTER',
    'email' => $email,
    'redirect_fail' => generate_url('register'),
    'redirect_success' => generate_url('home'),
    'procedure' => 'sp_utente_register',
    'in' => [
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
    ]
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'EMAIL È VALIDA
$pipeline->check(
    !filter_var($email, FILTER_VALIDATE_EMAIL),
    "L'indirizzo email inserito non è valido. Riprova."
);

// L'UTENTE È MAGGIORENNE
$pipeline->check(
    $anno_nascita > date('Y') - 18,
    "Devi essere maggiorenne per registrarti."
);

// LE PASSWORD CORRISPONDONO
$pipeline->check(
    $password !== $conferma_password,
    "Le password non corrispondono. Riprova."
);

// SE L'UTENTE È UN AMMINISTRATORE, DEVE INSERIRE UN CODICE DI SICUREZZA VALIDO
$pipeline->check(
    $is_admin && (empty($codice_sicurezza) || strlen($codice_sicurezza) < 8),
    "Devi inserire un codice di sicurezza valido per registrarti come amministratore."
);

// === ACTION ===
// REGISTRAZIONE DELL'UTENTE
$pipeline->invoke();

// === SUCCESS ===
// IMPOSTO LE VARIABILI DI SESSIONE
$_SESSION['email'] = $email;
$_SESSION['nickname'] = $nickname;
$_SESSION['nome'] = $nome;
$_SESSION['cognome'] = $cognome;
$_SESSION['anno_nascita'] = $anno_nascita;
$_SESSION['luogo_nascita'] = $luogo_nascita;
$_SESSION['is_creatore'] = $is_creatore;
$_SESSION['is_admin'] = $is_admin;

// REDIRECT ALLA PAGINA HOME
$pipeline->continue("Registrazione effettuata con successo.");