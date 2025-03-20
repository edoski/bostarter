<?php
/**
 * ACTION: progetto_budget_update
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_budget.php
 *
 * PURPOSE:
 * - Aggiorna il budget di un progetto.
 * - Solo il creatore del progetto può aggiornare il budget.
 * - Se l'operazione va a buon fine, il budget viene aggiornato nella tabella "PROGETTO".
 * - Per progetti hardware, il budget deve essere maggiore o uguale al costo totale delle componenti.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_progetto_budget_update".
 *
 * VARIABLES:
 * - nome: Nome del progetto
 * - budget: Nuovo budget del progetto
 * - tipo: Tipo del progetto (SOFTWARE, HARDWARE)
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'budget', 'tipo']);
$nome_progetto = $_POST['nome'];
$budget = floatval($_POST['budget']);
$tipo = $_POST['tipo'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_progetto_budget_update',
    'in' => [
        'p_nome_progetto' => $nome_progetto,
        'p_budget' => $budget,
        'p_email' => $email
    ]
];
$pipeline = new EventPipeline($context);

// === ACTION ===
// AGGIORNAMENTO DEL BUDGET
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Budget aggiornato con successo.");