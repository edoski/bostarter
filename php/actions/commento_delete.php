<?php
/**
 * ACTION: commento_delete
 * PERFORMED BY: UTENTE, ADMIN
 * UI: public/progetto_dettagli.php
 *
 * PURPOSE:
 * - Elimina un commento da parte di un utente.
 * - L'utente può eliminare solo i propri commenti, oppure un admin può eliminare qualsiasi commento.
 * - Se l'operazione va a buon fine, il commento viene eliminato dalla tabella "COMMENTO", altrimenti viene restituito un messaggio di errore.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_commento_delete".
 *
 * VARIABLES:
 * - id_commento: ID del commento da eliminare
 * - nome_progetto: Nome del progetto a cui si riferisce il commento
 * - email_autore: Email dell'utente che ha scritto il commento
 * - email: Email dell'utente che vuole eliminare il commento (idealmente = a email_autore)
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['id_commento', 'nome_progetto', 'email_autore']);
$id_commento = $_POST['id_commento'];
$nome_progetto = $_POST['nome_progetto'];
$email_autore = $_POST['email_autore'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMMENTO',
    'action' => 'DELETE',
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_commento_delete',
    'in' => [
        'p_id' => $id_commento,
        'p_email' => $email,
        'p_nome_progetto' => $nome_progetto
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È L'AUTORE DEL COMMENTO O ADMIN
$pipeline->check(
    !($_SESSION['is_admin'] || $email_autore === $email),
    "Non sei autorizzato ad effettuare questa operazione."
);

// === ACTION ===
// ELIMINAZIONE DEL COMMENTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Commento eliminato con successo.");