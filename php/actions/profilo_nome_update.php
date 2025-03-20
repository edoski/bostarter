<?php
/**
 * ACTION: profilo_nome_update
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_profili.php
 *
 * PURPOSE:
 * - Aggiorna il nome di un profilo esistente di un progetto software.
 * - Solo il creatore del progetto può aggiornare profili.
 * - Se l'operazione va a buon fine, il nome del profilo viene aggiornato nella tabella "PROFILO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_profilo_nome_update".
 *
 * VARIABLES:
 * - profilo: Nome attuale del profilo da aggiornare
 * - nome_progetto: Nome del progetto software a cui appartiene il profilo
 * - nuovo_nome: Nuovo nome del profilo
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['profilo', 'nome_progetto', 'nuovo_nome']);
$profilo = $_POST['profilo'];
$nome_progetto = $_POST['nome_progetto'];
$nuovo_nome = $_POST['nuovo_nome'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PROFILO',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect_fail' => generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $nome_progetto]),
    'redirect_success' => generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $nome_progetto, 'profilo' => $nuovo_nome]),
    'procedure' => 'sp_profilo_nome_update',
    'in' => [
        'p_nome_profilo' => $profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_nuovo_nome' => $nuovo_nome,
        'p_email_creatore' => $email
    ]
];
$pipeline = new EventPipeline($context);

// === ACTION ===
// AGGIORNAMENTO DEL NOME DEL PROFILO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROFILO AGGIORNATO
$pipeline->continue("Nome del profilo aggiornato con successo.");