<?php
/**
 * ACTION: utente_convert_creatore
 * PERFOMED BY: UTENTE, ADMIN
 * UI: public/progetti.php
 *
 * PURPOSE:
 * - Converte un utente normale in un utente creatore.
 * - Qualsiasi utente può richiedere di diventare creatore.
 * - Se l'operazione va a buon fine, l'utente viene inserito nella tabella "CREATORE".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_util_utente_convert_creatore".
 *
 * VARIABLES:
 * - email: Email dell'utente da convertire in creatore
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'UTENTE',
    'action' => 'CONVERT',
    'email' => $email,
    'redirect_fail' => generate_url('progetti'),
    'redirect_success' => generate_url('home'),
    'procedure' => 'sp_util_utente_convert_creatore',
    'in' => ['p_email' => $email]
];
$pipeline = new ActionPipeline($context);

// === VALIDATION ===
// L'UTENTE NON È GIÀ UN CREATORE
$pipeline->check(
    isset($_SESSION['is_creatore']) && $_SESSION['is_creatore'],
    "Sei già un utente creatore."
);

// === ACTION ===
// AGGIORNAMENTO DEL RUOLO DELL'UTENTE A CREATORE
$pipeline->invoke();
$_SESSION['is_creatore'] = true;

// DATI AGGIUNTIVI DA LOGGARE
$logs = [
    'email' => $email,
    'is_creatore' => true
];

// === SUCCESS ===
// REDIRECT ALLA PAGINA HOME
$pipeline->continue("Ruolo aggiornato a creatore con successo.", $logs);