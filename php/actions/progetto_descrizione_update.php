<?php
/**
 * ACTION: progetto_descrizione_update
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_descrizione.php
 *
 * PURPOSE:
 * - Aggiorna la descrizione di un progetto.
 * - Solo il creatore del progetto può aggiornare la descrizione.
 * - Se l'operazione va a buon fine, la descrizione viene aggiornata nella tabella "PROGETTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_progetto_descrizione_update".
 *
 * VARIABLES:
 * - nome: Nome del progetto
 * - descrizione: Nuova descrizione del progetto
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'descrizione']);
$nome_progetto = $_POST['nome'];
$descrizione = $_POST['descrizione'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_progetto_descrizione_update',
    'in' => [
        'p_nome' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_descrizione' => $descrizione
    ]
];
$pipeline = new ActionPipeline($context);

// === VALIDATION ===
// LA DESCRIZIONE È VALIDA
$pipeline->check(
    empty($descrizione),
    "La descrizione non può essere vuota."
);

// === ACTION ===
// AGGIORNAMENTO DELLA DESCRIZIONE
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Descrizione aggiornata con successo.");