<?php
/**
 * ACTION: skill_profilo_insert
 * PERFORMED BY: CREATORE
 * UI: public/progetto_aggiorna.php (attr=profili)
 *
 * PURPOSE:
 * - Inserisce una nuova competenza per un profilo di un progetto software.
 * - Solo il creatore del progetto può inserire competenze nei profili.
 * - Se l'operazione va a buon fine, la competenza viene inserita nella tabella "SKILL_PROFILO".
 * - Se ci sono candidature che non soddisfano il livello richiesto, vengono automaticamente rifiutate.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_profilo_insert".
 *
 * VARIABLES:
 * - nome_profilo: Nome del profilo
 * - nome_progetto: Nome del progetto software
 * - email_creatore: Email dell'utente creatore del progetto
 * - competenza: Competenza da inserire nel profilo
 * - livello_richiesto: Livello richiesto per la competenza (da 0 a 5)
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_profilo', 'competenza', 'livello']);
$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];
$livello = intval($_POST['livello']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_PROFILO',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $nome_progetto, 'profilo' => $nome_profilo]),
    'procedure' => 'sp_skill_profilo_insert',
    'in' => [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_competenza' => $competenza,
        'p_livello_richiesto' => $livello
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei autorizzato ad effettuare questa operazione."
);

// IL LIVELLO È UN INTERO COMPRESO TRA 0 E 5
$pipeline->check(
    !is_numeric($livello) || $livello < 0 || $livello > 5,
    "Il livello richiesto deve essere un intero compreso tra 0 e 5."
);

// === ACTION ===
// INSERIMENTO DELLA COMPETENZA NEL PROFILO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO DEL PROFILO
$pipeline->continue("Competenza inserita nel profilo con successo.");