<?php
/**
 * ACTION: skill_curriculum_insert
 * PERFORMED BY: ALL
 * UI: public/curriculum.php
 *
 * PURPOSE:
 * - Inserisce una nuova competenza nel curriculum di un utente.
 * - Qualsiasi utente può inserire competenze nel proprio curriculum.
 * - Se l'operazione va a buon fine, la competenza viene inserita nella tabella "SKILL_CURRICULUM".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_curriculum_insert".
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - competenza: Competenza da inserire nel curriculum
 * - livello: Livello di competenza (da 0 a 5)
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['competenza', 'livello']);
$competenza = $_POST['competenza'];
$livello = $_POST['livello'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_CURRICULUM',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('curriculum'),
    'procedure' => 'sp_skill_curriculum_insert',
    'in' => [
        'p_email' => $email,
        'p_competenza' => $competenza,
        'p_livello' => $livello
    ]
];
$pipeline = new ActionPipeline($context);

// === VALIDATION ===
// IL LIVELLO È UN INTERO COMPRESO TRA 0 E 5
$pipeline->check(
    !is_numeric($livello) || $livello < 0 || $livello > 5,
    "Il livello di competenza deve essere un numero intero compreso tra 0 e 5."
);

// === ACTION ===
// INSERIMENTO DELLA SKILL NEL CURRICULUM
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL CURRICULUM
$pipeline->continue("Skill aggiunta al curriculum con successo.");