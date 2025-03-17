<?php
/**
 * ACTION: componente_update
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_componenti.php
 *
 * PURPOSE:
 * - Aggiorna un componente esistente di un progetto hardware.
 * - Solo il creatore del progetto può aggiornare componenti.
 * - Se l'operazione va a buon fine, il componente viene aggiornato nella tabella "COMPONENTE".
 * - Se il costo totale dei componenti supera il budget del progetto, il budget viene automaticamente aumentato.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_componente_update".
 *
 * VARIABLES:
 * - nome_progetto: Nome del progetto hardware a cui appartiene il componente
 * - nome_componente: Nome attuale del componente da aggiornare
 * - nuovo_nome_componente: Nuovo nome del componente (può essere uguale al nome attuale)
 * - descrizione: Nuova descrizione del componente
 * - quantita: Nuova quantità del componente
 * - prezzo: Nuovo prezzo unitario del componente
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_componente', 'nuovo_nome_componente', 'nome_progetto', 'descrizione', 'quantita', 'prezzo']);
$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$nuovo_nome_componente = $_POST['nuovo_nome_componente'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMPONENTE',
    'action' => 'UPDATE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto]),
    'procedure' => 'sp_componente_update',
    'in' => [
        'p_nome_componente' => $nome_componente,
        'p_nuovo_nome_componente' => $nuovo_nome_componente,
        'p_nome_progetto' => $nome_progetto,
        'p_descrizione' => $descrizione,
        'p_quantita' => $quantita,
        'p_prezzo' => $prezzo,
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
// AGGIORNAMENTO DEL COMPONENTE ESISTENTE
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO COMPONENTI
$pipeline->continue("Componente aggiornato con successo.");