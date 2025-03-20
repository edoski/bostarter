<?php
/**
 * ACTION: skill_insert
 * PERFORMED BY: ADMIN
 * UI: public/curriculum.php
 *
 * PURPOSE:
 * - Inserisce una nuova competenza nella lista globale delle competenze disponibili.
 * - Solo gli amministratori possono inserire nuove competenze.
 * - Se l'operazione va a buon fine, la competenza viene inserita nella tabella "SKILL".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_insert".
 *
 * VARIABLES:
 * - competenza: Competenza da inserire nella lista globale
 * - email: Email dell'amministratore
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['competenza']);
$competenza = $_POST['competenza'];
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('curriculum'),
    'procedure' => 'sp_skill_insert',
    'in' => [
        'p_competenza' => $competenza,
        'p_email' => $email
    ]
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN AMMINISTRATORE
$pipeline->check(
    !isset($is_admin) || !$is_admin,
    "Non sei autorizzato ad effettuare questa operazione."
);

// LA COMPETENZA NON È VUOTA
$pipeline->check(
    !isset($competenza) || strlen($competenza) < 1,
    "Il nome della competenza non può essere vuoto."
);

// === ACTION ===
// INSERIMENTO DELLA SKILL NELLA LISTA GLOBALE
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL CURRICULUM
$pipeline->continue("Competenza inserita nella lista globale con successo.");