<?php
/**
 * ACTION: candidatura_insert
 * PERFORMED BY: ALL
 * UI: public/progetto_dettagli.php
 *
 * PURPOSE:
 * - Invia una candidatura da parte di un utente per partecipare ad un progetto software.
 * - Se l'operazione va a buon fine, l'utente viene inserito nella tabella "PARTECIPANTE", altrimenti viene restituito un messaggio di errore.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_partecipante_utente_insert".
 *
 * VARIABLES:
 * - email: Email dell'utente che invia la candidatura
 * - nome_progetto: Nome del progetto a cui l'utente vuole partecipare
 * - nome_profilo: Nome del profilo che l'utente vuole ricoprire nel progetto
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_profilo']);
$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PARTECIPANTE',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_partecipante_utente_insert',
    'in' => [
        'p_email' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_nome_profilo' => $nome_profilo
    ]
];
$pipeline = new ActionPipeline($context);

// === ACTION ===
// INVIO LA CANDIDATURA
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Candidatura inviata con successo.");