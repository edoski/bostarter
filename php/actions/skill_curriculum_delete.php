<?php
/**
 * ACTION: skill_curriculum_delete
 * PERFORMED BY: ALL
 * UI: public/curriculum.php
 *
 * PURPOSE:
 * - Rimuove una competenza dal curriculum di un utente.
 * - Qualsiasi utente può rimuovere competenze dal proprio curriculum.
 * - Se l'operazione va a buon fine, la competenza viene rimossa dalla tabella "SKILL_CURRICULUM".
 * - Se l'utente è un partecipante a progetti che richiedono questa skill, le candidature vengono automaticamente rifiutate.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_curriculum_delete".
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - competenza: Competenza da rimuovere dal curriculum
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['competenza']);
$competenza = $_POST['competenza'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_CURRICULUM',
    'action' => 'DELETE',
    'redirect' => generate_url('curriculum'),
    'procedure' => 'sp_skill_curriculum_delete',
    'in' => [
        'p_email' => $email,
        'p_competenza' => $competenza
    ]
];
$pipeline = new ValidationPipeline($context);

// === ACTION ===
// RIMOZIONE DELLA SKILL DAL CURRICULUM
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL CURRICULUM
$pipeline->continue("Skill rimossa dal curriculum con successo.");