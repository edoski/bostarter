<?php
/**
 * ACTION: skill_curriculum_update
 * PERFORMED BY: ALL
 * UI: public/curriculum.php
 *
 * PURPOSE:
 * - Aggiorna il livello di una competenza nel curriculum di un utente.
 * - Qualsiasi utente può aggiornare le proprie competenze.
 * - Se l'operazione va a buon fine, il livello della competenza viene aggiornato nella tabella "SKILL_CURRICULUM".
 * - Se il nuovo livello è inferiore a quello richiesto da progetti a cui l'utente si è candidato, le candidature vengono automaticamente rifiutate.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_curriculum_update".
 *
 * VARIABLES:
 * - email: Email dell'utente
 * - competenza: Competenza da aggiornare
 * - livello: Nuovo livello di competenza (da 0 a 5)
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['competenza', 'livello']);
$competenza = $_POST['competenza'];
$livello = intval($_POST['livello']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_CURRICULUM',
    'action' => 'UPDATE',
    'redirect' => generate_url('curriculum'),
    'procedure' => 'sp_skill_curriculum_update',
    'in' => [
        'p_email' => $email,
        'p_competenza' => $competenza,
        'p_livello' => $livello
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// IL LIVELLO È UN INTERO COMPRESO TRA 0 E 5
$pipeline->check(
    !is_numeric($livello) || $livello < 0 || $livello > 5,
    "Il livello di competenza deve essere un numero intero compreso tra 0 e 5."
);

// === ACTION ===
// AGGIORNAMENTO DEL LIVELLO DELLA SKILL NEL CURRICULUM
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL CURRICULUM
$pipeline->continue("Livello di competenza aggiornato con successo.");