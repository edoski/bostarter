<?php
/**
 * ACTION: commento_insert
 * PERFORMED BY: ALL
 * UI: public/progetto_dettagli.php
 *
 * PURPOSE:
 * - Inserisce un nuovo commento per un progetto.
 * - Qualsiasi utente può commentare qualsiasi progetto.
 * - Se l'operazione va a buon fine, il commento viene inserito nella tabella "COMMENTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_commento_insert".
 *
 * VARIABLES:
 * - email: Email dell'utente che scrive il commento
 * - nome_progetto: Nome del progetto a cui si riferisce il commento
 * - commento: Testo del commento
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'commento']);
$email = $_SESSION['email'];
$nome_progetto = $_POST['nome_progetto'];
$commento = $_POST['commento'];

// === CONTEXT ===
$context = [
    'collection' => 'COMMENTO',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_commento_insert',
    'in' => [
        'p_email_utente' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_commento' => $commento
    ]
];
$pipeline = new ActionPipeline($context);

// === VALIDATION ===
// IL COMMENTO NON PUÒ ESSERE VUOTO
$pipeline->check(
    strlen(trim($commento)) < 1,
    "Il commento non può essere vuoto."
);

// === ACTION ===
// INSERIMENTO DEL COMMENTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Commento inserito con successo.");