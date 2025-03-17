<?php
/**
 * ACTION: skill_profilo_delete
 * PERFORMED BY: CREATORE
 * UI: public/progetto_aggiorna.php (attr=profili)
 *
 * PURPOSE:
 * - Rimuove una competenza da un profilo di un progetto software.
 * - Solo il creatore del progetto può rimuovere competenze dai profili.
 * - Se l'operazione va a buon fine, la competenza viene rimossa dalla tabella "SKILL_PROFILO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_profilo_delete".
 *
 * VARIABLES:
 * - nome_profilo: Nome del profilo
 * - nome_progetto: Nome del progetto software
 * - competenza: Competenza da rimuovere dal profilo
 * - email_creatore: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_profilo', 'competenza']);
$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_PROFILO',
    'action' => 'DELETE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $nome_progetto, 'profilo' => $nome_profilo]),
    'procedure' => 'sp_skill_profilo_delete',
    'in' => [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_competenza' => $competenza,
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
// RIMOZIONE DELLA COMPETENZA DAL PROFILO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO DEL PROFILO
$pipeline->continue("Competenza rimossa dal profilo con successo.");