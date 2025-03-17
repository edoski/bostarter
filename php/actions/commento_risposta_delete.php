<?php
/**
 * ACTION: commento_risposta_delete
 * PERFORMED BY: CREATORE, ADMIN
 * UI: public/progetto_dettagli.php
 *
 * PURPOSE:
 * - Elimina la risposta a un commento per un progetto.
 * - Solo il creatore del progetto o un admin possono eliminare le risposte ai commenti.
 * - Se l'operazione va a buon fine, il campo "risposta" viene impostato a NULL nella tabella "COMMENTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_commento_risposta_delete".
 *
 * VARIABLES:
 * - id_commento: ID del commento di cui eliminare la risposta
 * - email: Email del creatore del progetto
 * - nome_progetto: Nome del progetto a cui si riferisce il commento
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['id_commento', 'nome_progetto']);
$id_commento = $_POST['id_commento'];
$nome_progetto = $_POST['nome_progetto'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMMENTO',
    'action' => 'DELETE',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_commento_risposta_delete',
    'in' => [
        'p_commento_id' => $id_commento,
        'p_email_creatore' => $email,
        'p_nome_progetto' => $nome_progetto
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO O ADMIN
$pipeline->check(
    !($_SESSION['is_admin'] || is_progetto_owner($email, $nome_progetto)),
    "Non sei autorizzato ad effettuare questa operazione."
);

// === ACTION ===
// CANCELLAZIONE DELLA RISPOSTA
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Risposta eliminata con successo.");