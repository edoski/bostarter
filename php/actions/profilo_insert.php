<?php
/**
 * ACTION: profilo_insert
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_profili.php
 *
 * PURPOSE:
 * - Inserisce un nuovo profilo per un progetto software.
 * - Solo il creatore del progetto può inserire profili.
 * - Se l'operazione va a buon fine, il profilo viene inserito nella tabella "PROFILO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_profilo_insert".
 *
 * VARIABLES:
 * - nome_profilo: Nome del profilo da inserire
 * - nome_progetto: Nome del progetto software a cui appartiene il profilo
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_profilo', 'nome_progetto']);
$nome_profilo = $_POST['nome_profilo'];
$nome_progetto = $_POST['nome_progetto'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PROFILO',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['nome' => $nome_progetto, 'attr' => 'profili']),
    'procedure' => 'sp_profilo_insert',
    'in' => [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei autorizzato ad effettuare questa operazione."
);

// === ACTION ===
// INSERIMENTO DEL PROFILO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO PROFILI
$pipeline->continue("Profilo inserito con successo. Aggiungi ora le skill richieste.");