<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_profilo', 'competenza', 'livello']);

$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];
$livello = intval($_POST['livello']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($nome_progetto);

// 4. Il livello è valido
if ($livello < 0 || $livello > 5) {
    redirect(
        false,
        "Il livello deve essere compreso tra 0 e 5.",
        "../public/progetti.php"
    );
}

// === ACTION ===
// Inserimento della competenza nel profilo del progetto
try {
    $in = [
        'p_nome_profilo' => $nome_profilo,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $_SESSION['email'],
        'p_competenza' => $competenza,
        'p_livello_richiesto' => $livello
    ];

    sp_invoke('sp_skill_profilo_insert', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiunta della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
    );
}

// Success, redirect alla pagina del profilo
redirect(
    true,
    "Competenza aggiunta con successo al profilo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
);