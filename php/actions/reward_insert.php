<?php
/**
 * ACTION: reward_insert
 * PERFORMED BY: CREATORE
 * UI: public/progetto_aggiorna.php (attr=reward)
 *
 * PURPOSE:
 * - Inserisce una nuova reward per un progetto.
 * - Solo il creatore del progetto può inserire reward.
 * - Se l'operazione va a buon fine, la reward viene inserita nella tabella "REWARD".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_reward_insert".
 *
 * VARIABLES:
 * - codice: Codice identificativo della reward
 * - nome_progetto: Nome del progetto a cui appartiene la reward
 * - email: Email dell'utente creatore del progetto
 * - descrizione: Descrizione della reward
 * - foto: Immagine della reward
 * - min_importo: Importo minimo per ottenere la reward
 */

// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['codice', 'nome', 'descrizione', 'min_importo']);
$codice = $_POST['codice'];
$nome_progetto = $_POST['nome'];
$email = $_SESSION['email'];
$descrizione = $_POST['descrizione'];
$foto = file_get_contents($_FILES['foto']['tmp_name']);
$min_importo = floatval($_POST['min_importo']);

// === CONTEXT ===
$context = [
    'collection' => 'REWARD',
    'action' => 'INSERT',
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto]),
    'procedure' => 'sp_reward_insert',
    'in' => [
        'p_codice' => $codice,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_descrizione' => $descrizione,
        'p_foto' => $foto,
        'p_min_importo' => $min_importo
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei autorizzato ad effettuare questa operazione."
);

// L'IMMAGINE È STATA CARICATA CORRETTAMENTE
$pipeline->check(
    !isset($_FILES['foto']) || $_FILES['foto']['error'] != UPLOAD_ERR_OK,
    "Errore durante il caricamento dell'immagine. Riprova."
);

// L'IMMAGINE È VALIDA
$pipeline->check(
    !$foto,
    "Errore durante la lettura dell'immagine. Riprova."
);

// L'IMPORTO MINIMO È VALIDO
$pipeline->check(
    $min_importo < 0.01,
    "L'importo minimo deve essere maggiore o uguale a 0.01."
);

// === ACTION ===
// INSERIMENTO DELLA REWARD
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Reward inserita con successo.");