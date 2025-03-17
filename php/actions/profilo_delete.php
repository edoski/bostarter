<?php
/**
 * ACTION: profilo_delete
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_profili.php
 *
 * PURPOSE:
 * - Rimuove un profilo da un progetto software.
 * - Solo il creatore del progetto può rimuovere profili.
 * - Se l'operazione va a buon fine, il profilo viene rimosso dalla tabella "PROFILO".
 * - Tutte le candidature associate a questo profilo vengono rimosse automaticamente.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_profilo_delete".
 *
 * VARIABLES:
 * - nome_profilo: Nome del profilo da rimuovere
 * - nome_progetto: Nome del progetto software a cui appartiene il profilo
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_profilo', 'nome_progetto']);
$nome_profilo = $_POST['nome_profilo'];
$nome_progetto = $_POST['nome_progetto'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PROFILO',
    'action' => 'DELETE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['nome' => $nome_progetto, 'attr' => 'profili']),
    'procedure' => 'sp_profilo_delete',
    'in' => [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email
    ]
];
$pipeline = new ActionPipeline($context);

// === ACTION ===
// ELIMINAZIONE DEL PROFILO (+ SKILLS & CANDIDATURE ASSOCIATE)
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO PROFILI
$pipeline->continue("Profilo eliminato con successo.");