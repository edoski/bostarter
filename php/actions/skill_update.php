<?php
/**
 * ACTION: skill_update
 * PERFOMED BY: ADMIN
 * UI: public/curriculum.php
 *
 * PURPOSE:
 * - Aggiorna il nome di una competenza nella lista globale delle competenze.
 * - Solo gli amministratori possono aggiornare i nomi delle competenze.
 * - Se l'operazione va a buon fine, il nome della competenza viene aggiornato nella tabella "SKILL".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_update".
 *
 * VARIABLES:
 * - email_admin: Email dell'amministratore
 * - vecchia_competenza: Nome attuale della competenza
 * - nuova_competenza: Nuovo nome della competenza
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['vecchia_competenza', 'nuova_competenza']);
$vecchia_competenza = $_POST['vecchia_competenza'];
$nuova_competenza = $_POST['nuova_competenza'];
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect' => generate_url('curriculum'),
    'procedure' => 'sp_skill_update',
    'in' => [
        'p_email_admin' => $email,
        'p_vecchia_competenza' => $vecchia_competenza,
        'p_nuova_competenza' => $nuova_competenza
    ]
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN AMMINISTRATORE
$pipeline->check(
    !isset($is_admin) || !$is_admin,
    "Non sei autorizzato ad effettuare questa operazione."
);

// LA NUOVA COMPETENZA NON È VUOTA
$pipeline->check(
    !isset($nuova_competenza) || strlen($nuova_competenza) < 1,
    "Il nome della competenza non può essere vuoto."
);

// === ACTION ===
// AGGIORNAMENTO DEL NOME DELLA SKILL NELLA LISTA GLOBALE
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL CURRICULUM
$pipeline->continue("Competenza '$vecchia_competenza' aggiornata a '$nuova_competenza' con successo.");