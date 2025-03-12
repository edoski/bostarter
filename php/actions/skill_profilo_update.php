<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. Le variabili POST sono state impostate correttamente
checkSetVars(['nome_progetto', 'nome_profilo', 'competenza', 'nuovo_livello']);

$nome_progetto = $_POST['nome_progetto'];
$nome_profilo = $_POST['nome_profilo'];
$competenza = $_POST['competenza'];
$nuovo_livello = intval($_POST['nuovo_livello']);

// 3. L'utente è il creatore del progetto
checkProgettoOwner($nome_progetto);

// 4. Il nuovo livello è valido
if ($nuovo_livello < 0 || $nuovo_livello > 5) {
    redirect(
        false,
        "Il livello deve essere compreso tra 0 e 5.",
        "../public/progetti.php"
    );
}

// === ACTION ===
// Aggiornamento del livello della competenza nel profilo del progetto
try {
    $in = [
        'p_nome_profilo' => $nome_profilo,
        'p_competenza' => $competenza,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $_SESSION['email'],
        'p_nuovo_livello_richiesto' => $nuovo_livello
    ];

    sp_invoke('sp_skill_profilo_update', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante l'aggiornamento della competenza: " . $ex->errorInfo[2],
        "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
    );
}

// Success, redirect alla pagina del profilo
redirect(
    true,
    "Livello della competenza aggiornato con successo.",
    "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($nome_progetto) . "&profilo=" . urlencode($nome_profilo)
);