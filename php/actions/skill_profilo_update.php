<?php
/**
 * ACTION: skill_profilo_update
 * PERFORMED BY: CREATORE
 * UI: public/progetto_aggiorna.php (attr=profili)
 *
 * PURPOSE:
 * - Aggiorna il livello richiesto di una competenza per un profilo di un progetto software.
 * - Solo il creatore del progetto può aggiornare i livelli richiesti.
 * - Se l'operazione va a buon fine, il livello richiesto viene aggiornato nella tabella "SKILL_PROFILO".
 * - Se ci sono candidature che non soddisfano il nuovo livello richiesto, vengono automaticamente rifiutate.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_skill_profilo_update".
 *
 * VARIABLES:
 * - nome_profilo: Nome del profilo
 * - competenza: Competenza da aggiornare
 * - nome_progetto: Nome del progetto software
 * - email_creatore: Email dell'utente creatore del progetto
 * - nuovo_livello_richiesto: Nuovo livello richiesto per la competenza (da 0 a 5)
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_profilo', 'competenza', 'nuovo_livello']);
$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];
$nuovo_livello = intval($_POST['nuovo_livello']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'SKILL_PROFILO',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $nome_progetto, 'profilo' => $nome_profilo]),
    'procedure' => 'sp_skill_profilo_update',
    'in' => [
        'p_nome_profilo' => $nome_profilo,
        'p_competenza' => $competenza,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_nuovo_livello_richiesto' => $nuovo_livello
    ]
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// IL NUOVO LIVELLO È UN INTERO COMPRESO TRA 0 E 5
$pipeline->check(
    !is_numeric($nuovo_livello) || $nuovo_livello < 0 || $nuovo_livello > 5,
    "Il livello richiesto deve essere un intero compreso tra 0 e 5."
);

// === ACTION ===
// AGGIORNAMENTO DEL LIVELLO DELLA COMPETENZA NEL PROFILO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO DEL PROFILO
$pipeline->continue("Livello richiesto per '$nome_profilo' aggiornato a $nuovo_livello/5 con successo.");