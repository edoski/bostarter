<?php
/**
 * ACTION: componente_insert
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_componenti.php
 *
 * PURPOSE:
 * - Inserisce un nuovo componente per un progetto hardware.
 * - Solo il creatore del progetto può inserire componenti.
 * - Se l'operazione va a buon fine, il componente viene inserito nella tabella "COMPONENTE".
 * - Se il costo totale dei componenti supera il budget del progetto, il budget viene automaticamente aumentato.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_componente_insert".
 *
 * VARIABLES:
 * - nome_componente: Nome del componente da inserire
 * - nome_progetto: Nome del progetto hardware a cui appartiene il componente
 * - descrizione: Descrizione del componente
 * - quantita: Quantità del componente
 * - prezzo: Prezzo unitario del componente
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome_progetto', 'nome_componente', 'descrizione', 'quantita', 'prezzo']);
$nome_progetto = $_POST['nome_progetto'];
$nome_componente = $_POST['nome_componente'];
$descrizione = $_POST['descrizione'];
$quantita = intval($_POST['quantita']);
$prezzo = floatval($_POST['prezzo']);
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'COMPONENTE',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $nome_progetto]),
    'procedure' => 'sp_componente_insert',
    'in' => [
        'p_nome_componente' => $nome_componente,
        'p_nome_progetto' => $nome_progetto,
        'p_descrizione' => $descrizione,
        'p_quantita' => $quantita,
        'p_prezzo' => $prezzo,
        'p_email_creatore' => $email
    ]
];
$pipeline = new EventPipeline($context);

// === ACTION ===
// INSERIMENTO DEL COMPONENTE NEL PROGETTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO COMPONENTI
$pipeline->continue("Componente inserito con successo.");