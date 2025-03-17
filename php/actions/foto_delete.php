<?php
/**
 * ACTION: foto_delete
 * PERFORMED BY: CREATORE
 * UI: components/progetto_aggiorna_descrizione.php
 *
 * PURPOSE:
 * - Elimina una foto di un progetto.
 * - Solo il creatore del progetto può eliminare le foto.
 * - Se l'operazione va a buon fine, la foto viene eliminata dalla tabella "FOTO".
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_foto_delete".
 *
 * VARIABLES:
 * - nome_progetto: Nome del progetto da cui eliminare la foto
 * - email: Email del creatore del progetto
 * - foto_id: ID della foto da eliminare
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'id']);
$nome_progetto = $_POST['nome'];
$foto_id = $_POST['id'];
$email = $_SESSION['email'];

// === CONTEXT ===
$context = [
    'collection' => 'FOTO',
    'action' => 'DELETE',
    'email' => $email,
    'redirect' => generate_url('progetto_aggiorna', ['nome' => $nome_progetto, 'attr' => 'descrizione']),
    'procedure' => 'sp_foto_delete',
    'in' => [
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email,
        'p_foto_id' => $foto_id
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

// === ACTION ===
// ELIMINAZIONE DELLA FOTO
$pipeline->invoke();

// === SUCCESS ===
// REDIRECT ALLA PAGINA DI AGGIORNAMENTO DESCRIZIONE
$pipeline->continue("Foto eliminata con successo.");