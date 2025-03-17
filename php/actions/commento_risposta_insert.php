<?php
/**
 * ACTION: commento_risposta_insert
 * PERFORMED BY: CREATORE
 * UI: public/progetto_dettagli.php
 *
 * PURPOSE:
 * - Inserisce una risposta a un commento per un progetto.
 * - Solo il creatore del progetto può rispondere ai commenti.
 * - Se l'operazione va a buon fine, la risposta viene inserita nel campo "risposta" della tabella "COMMENTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_commento_risposta_insert".
 *
 * VARIABLES:
 * - id_commento: ID del commento a cui si vuole rispondere
 * - email: Email del creatore del progetto
 * - nome_progetto: Nome del progetto a cui si riferisce il commento
 * - risposta: Testo della risposta al commento
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['id_commento', 'nome_progetto', 'risposta']);
$id_commento = $_POST['id_commento'];
$nome_progetto = $_POST['nome_progetto'];
$risposta = $_POST['risposta'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMMENTO',
    'action' => 'REPLY',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_commento_risposta_insert',
    'in' => [
        'p_commento_id' => $id_commento,
        'p_email_creatore' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_risposta' => $risposta
    ]
];
$pipeline = new ActionPipeline($context);

// === ACTION ===
// INSERIMENTO DELLA RISPOSTA AL COMMENTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Risposta inserita con successo.");