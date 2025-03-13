<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. Le variabili POST sono state impostate correttamente
checkSetVars(
    ['email', 'password'],
    "../public/login.php"
);

// 2. Recupero i dati inviati dal form
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$codiceSicurezza = trim($_POST['codice_sicurezza'] ?? '') ?: null; // Se non è stato inviato, imposto a null

// 3. Controllo che non siano vuoti
if (empty($email) || empty($password)) {
    redirect(
        false,
        "Email e password sono obbligatori.",
        "../public/login.php"
    );
}

// === ACTION ===
// Login dell'utente
try {
    $in = [
        'p_email' => $email
    ];

    $outLogin = [
        'p_nickname_out' => null,
        'p_email_out' => null,
        'p_password_hash_out' => null
    ];

    sp_invoke('sp_utente_login', $in, $outLogin);
} catch (PDOException $ex) {
    redirect(
        false,
        "Email o password non valide.",
        "../public/login.php"
    );
}

// Verifico di aver ricevuto i dati
if (!$outLogin) {
    redirect(
        false,
        "Errore durante il login. Riprova.",
        "../public/login.php"
    );
}

// Verifico la password
if (!password_verify($password, $outLogin['p_password_hash_out'])) {
    redirect(
        false,
        "Email o password non valide.",
        "../public/login.php"
    );
}

// ADMIN CHECK
// Prima di impostare le variabili di sessione, controlliamo se l'utente è admin e se il codice di sicurezza è corretto
try {
    $in = ['p_email' => $outLogin['p_email_out']];
    // Restituisce un array con il campo booleano 'is_admin'
    $_SESSION['is_admin'] = sp_invoke('sp_util_admin_exists', $in)[0]['is_admin'];

    $outAdmin = ['p_codice_sicurezza_out' => null];
    sp_invoke('sp_util_admin_get_codice_sicurezza', $in, $outAdmin);

    if ($_SESSION['is_admin']) {
        if (empty($codiceSicurezza)) {
            redirect(
                false,
                "Devi inserire il codice di sicurezza per accedere come amministratore.",
                "../public/login.php"
            );
        }
        if (!password_verify($codiceSicurezza, $outAdmin['p_codice_sicurezza_out'])) {
            redirect(
                false,
                "Codice di sicurezza non valido.",
                "../public/login.php"
            );
        }
    }
} catch (PDOException $ex) {
    $_SESSION['is_admin'] = false;
    redirect(
        false,
        "Errore durante il controllo dell'amministratore: " . $ex->errorInfo[2],
        "../public/login.php"
    );
}

// SET SESSION VARIABLES AND RETRIEVE ADDITIONAL DATA
// Se arrivo qui, la password (e, se admin, il codice di sicurezza) sono corretti
$_SESSION['email'] = $outLogin['p_email_out'];
$_SESSION['nickname'] = $outLogin['p_nickname_out'];

// Recupero il resto delle informazioni dell'utente
try {
    $in = ['p_email' => $outLogin['p_email_out']];
    $datiUtente = sp_invoke('sp_utente_select', $in);

    if (!empty($datiUtente[0])) {
        $_SESSION['nome'] = $datiUtente[0]['nome'] ?? '';
        $_SESSION['cognome'] = $datiUtente[0]['cognome'] ?? '';
        $_SESSION['luogo_nascita'] = $datiUtente[0]['luogo_nascita'] ?? '';
        $_SESSION['anno_nascita'] = $datiUtente[0]['anno_nascita'] ?? '';
    }
} catch (PDOException $ex) {
    $_SESSION['nome'] = '';
    $_SESSION['cognome'] = '';
    $_SESSION['luogo_nascita'] = '';
    $_SESSION['anno_nascita'] = '';

    redirect(
        false,
        "Errore durante il recupero delle informazioni utente: " . $ex->errorInfo[2],
        "../public/home.php"
    );
}

// Controllo se l'utente è un creatore
try {
    $in = ['p_email' => $outLogin['p_email_out']];
    $_SESSION['is_creatore'] = sp_invoke('sp_util_creatore_exists', $in)[0]['is_creatore'];
} catch (PDOException $ex) {
    $_SESSION['is_creatore'] = false;
    redirect(
        false,
        "Errore durante il controllo del creatore: " . $ex->errorInfo[2],
        "../public/login.php"
    );
}

// Success, redirect alla pagina home
redirect(
    true,
    "Login effettuato con successo.",
    "../public/home.php"
);