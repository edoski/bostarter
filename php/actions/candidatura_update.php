<?php
/**
 * ACTION: candidatura_update
 * PERFORMED BY: CREATORE
 * UI: public/candidature.php
 *
 * PURPOSE:
 * - Aggiorna lo stato di una candidatura da parte di un utente per partecipare ad un progetto software.
 * - Il creatore del progetto può accettare o rifiutare la candidatura.
 * - Se l'operazione va a buon fine, la candidatura viene aggiornata nella tabella "PARTECIPANTE", altrimenti viene restituito un messaggio di errore.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_partecipante_creatore_update".
 *
 * VARIABLES:
 * - email: Email del creatore del progetto che accetta o rifiuta la candidatura
 * - email_candidato: Email dell'utente a cui si riferisce la candidatura
 * - nome_progetto: Nome del progetto a cui l'utente vuole partecipare
 * - nome_profilo: Nome del profilo che l'utente vuole ricoprire nel progetto
 * - nuovo_stato: Nuovo stato della candidatura (accettato/rifiutato)
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['email_candidato', 'nome_progetto', 'nome_profilo', 'nuovo_stato']);
$email = $_SESSION['email'];
$email_candidato = $_POST['email_candidato'];
$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$nuovo_stato = $_POST['nuovo_stato'];

// === CONTEXT ===
$context = [
    'collection' => 'PARTECIPANTE',
    'action' => 'UPDATE',
    'redirect' => generate_url('candidature'),
    'procedure' => 'sp_partecipante_creatore_update',
    'in' => [
        'p_email_creatore' => $email,
        'p_email_candidato' => $email_candidato,
        'p_nome_progetto' => $nome_progetto,
        'p_nome_profilo' => $nome_profilo,
        'p_nuovo_stato' => $nuovo_stato
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei autorizzato ad effettuare questa operazione."
);

// IL PROGETTO È APERTO
$pipeline->invoke('sp_util_progetto_is_aperto', ['p_nome_progetto' => $nome_progetto]);

// LO STATO È VALIDO (ACCETTATO/RIFIUTATO)
$pipeline->check(
    $nuovo_stato != 'accettato' && $nuovo_stato != 'rifiutato',
    "Stato '$nuovo_stato' non valido."
);

// === ACTION ===
// AGGIORNAMENTO DELLA CANDIDATURA
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT A CANDIDATURE
$pipeline->continue("Partecipante " . $nuovo_stato . " con successo.");