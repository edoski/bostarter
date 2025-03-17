<?php
/**
 * ACTION: componente_delete
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_componenti.php
 *
 * PURPOSE:
 * - Rimuove un componente da un progetto hardware.
 * - Solo il creatore del progetto può rimuovere componenti.
 * - Se l'operazione va a buon fine, il componente viene rimosso dalla tabella "COMPONENTE".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_componente_delete".
 *
 * VARIABLES:
 * - nome_componente: Nome del componente da rimuovere
 * - nome_progetto: Nome del progetto hardware a cui appartiene il componente
 * - email: Email dell'utente creatore del progetto che richiede la rimozione
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_componente']);
$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMPONENTE',
    'action' => 'DELETE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto]),
    'procedure' => 'sp_componente_delete',
    'in' => [
        'p_nome_componente' => $nome_componente,
        'p_nome_progetto' => $nome_progetto,
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
// RIMOZIONE DEL COMPONENTE DAL PROGETTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO COMPONENTI
$pipeline->continue("Componente rimosso con successo.");