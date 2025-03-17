<?php
/**
 * ACTION: progetto_insert
 * PERFORMED BY: CREATORE
 * UI: public/progetto_crea.php
 *
 * PURPOSE:
 * - Inserisce un nuovo progetto nella piattaforma.
 * - Solo utenti con ruolo di creatore possono inserire progetti.
 * - Se l'operazione va a buon fine, il progetto viene inserito nella tabella "PROGETTO" e nelle tabelle "PROGETTO_SOFTWARE" o "PROGETTO_HARDWARE" in base al tipo.
 * - Viene inserita una reward di default per il progetto.
 * - L'attributo nr_progetti dell'utente creatore viene automaticamente incrementato.
 * - Per maggiori dettagli, vedere la documentazione della stored procedure "sp_progetto_insert".
 *
 * VARIABLES:
 * - nome: Nome del progetto
 * - email: Email dell'utente creatore
 * - descrizione: Descrizione del progetto
 * - budget: Budget richiesto per il progetto
 * - data_limite: Data limite per il raggiungimento del budget
 * - tipo: Tipo di progetto ('software' o 'hardware')
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(['nome', 'descrizione', 'budget', 'data_limite', 'tipo']);
$nome = htmlspecialchars(trim($_POST['nome']));
$descrizione = htmlspecialchars(trim($_POST['descrizione']));
$budget = floatval($_POST['budget']);
$data_limite = trim($_POST['data_limite']);
$tipo = $_POST['tipo'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO',
    'action' => 'INSERT',
    'redirect_fail' => generate_url('progetto_crea'),
    'redirect_success' => generate_url('progetto_dettagli', ['nome' => $nome]),
    'procedure' => 'sp_progetto_insert',
    'in' => [
        'p_nome' => $nome,
        'p_email_creatore' => $_SESSION['email'],
        'p_descrizione' => $descrizione,
        'p_budget' => $budget,
        'p_data_limite' => $data_limite,
        'p_tipo' => $tipo
    ]
];
$pipeline = new ValidationPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN CREATORE
$pipeline->check(
    !isset($_SESSION['is_creatore']) || !$_SESSION['is_creatore'],
    "Non sei autorizzato ad effettuare questa operazione."
);

// LA DATA LIMITE È SUCCESSIVA AD OGGI
$pipeline->check(
    $data_limite <= date('Y-m-d'),
    "La data limite deve essere successiva ad oggi."
);

// IL TIPO È VALIDO (SOFTWARE O HARDWARE)
$pipeline->check(
    $tipo !== 'software' && $tipo !== 'hardware',
    "Il tipo di progetto deve essere 'software' o 'hardware'."
);

// === ACTION ===
// INSERIMENTO DEL PROGETTO
$pipeline->invoke();

// INSERIMENTO DELLA REWARD DI DEFAULT
$pipeline->check(
    !seed_progetto_default_reward($nome),
    "Errore durante l'inserimento della foto di default."
);

// === SUCCESS ===
// REDIRECT ALLA PAGINA DEL PROGETTO
$pipeline->continue("Progetto creato con successo. Per ciascuna sezione, clicca sul bottone giallo 'Modifica' per iniziare a personalizzare il progetto.");