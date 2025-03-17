<?php
/**
 * ACTION: foto_insert
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_descrizione.php
 *
 * PURPOSE:
 * - Inserisce una nuova foto per un progetto.
 * - Solo il creatore del progetto può inserire foto.
 * - Se l'operazione va a buon fine, la foto viene inserita nella tabella "FOTO".
 * - Per maggiori dettagli, vedere la documentazione delle stored procedure:"sp_foto_insert"
 *
 * VARIABLES:
 * - nome: Nome del progetto
 * - foto: Immagine caricata tramite form
 * - email: Email dell'utente creatore del progetto
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome']);
$nome_progetto = $_POST['nome'];
$email = $_SESSION['email'];
$foto = file_get_contents($_FILES['foto']['tmp_name']);

// === CONTEXT ===
$context = [
    'collection' => 'FOTO',
    'action' => 'INSERT',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['nome' => $nome_progetto, 'attr' => 'descrizione']),
    'procedure' => 'sp_foto_insert',
    'in' => [
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_foto' => $foto
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei autorizzato ad effettuare questa operazione."
);

// IL PROGETTO È APERTO
$pipeline->invoke('sp_util_progetto_is_aperto', ['p_nome_progetto' => $nome_progetto]);

// L'IMMAGINE È STATA CARICATA
$pipeline->check(
    !isset($_FILES['foto']) || $_FILES['foto']['error'] != UPLOAD_ERR_OK,
    "Errore nel caricamento dell'immagine."
);

// L'IMMAGINE È VALIDA
$pipeline->check(
    !$foto,
    "Errore nel recupero dei dati dell'immagine."
);

// === ACTION ===
// INSERIMENTO DELLA FOTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO DESCRIZIONE
$pipeline->continue("Foto inserita con successo.");