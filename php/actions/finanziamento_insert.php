<?php
/**
 * ACTION: finanziamento_insert
 * PERFORMED BY: ALL
 * UI: public/finanziamento_conferma.php
 *
 * PURPOSE:
 * - Inserisce un nuovo finanziamento per un progetto.
 * - Un utente può finanziare qualsiasi progetto aperto, inclusi i propri.
 * - Se l'operazione va a buon fine, il finanziamento viene inserito nella tabella "FINANZIAMENTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure: "sp_finanziamento_insert"
 *
 * VARIABLES:
 * - email: Email dell'utente che finanzia
 * - nome_progetto: Nome del progetto da finanziare
 * - codice_reward: Codice della reward scelta
 * - importo: Importo del finanziamento
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'importo', 'reward']);
$nome_progetto = $_POST['nome'];
$importo = floatval($_POST['importo']);
$reward = $_POST['reward'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'FINANZIAMENTO',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_finanziamento_insert',
    'in' => [
        'p_email' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_codice_reward' => $reward,
        'p_importo' => $importo
    ]
];
$pipeline = new ActionPipeline($context);

// === ACTION ===
// INSERIMENTO DEL FINANZIAMENTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Finanziamento effettuato con successo.");