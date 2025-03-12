<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del commento, oppure è un admin
if (!$_SESSION['is_admin']) {
    if (!($_POST['email_utente'] === $_SESSION['email'])) {
        redirect(
            false,
            "Non sei autorizzato ad effettuare questa operazione.",
            "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
        );
    }
}

// 3. È stato selezionato un progetto valido
if (!isset($_post['nome_progetto'])) {
    redirect(
        false,
        "errore selezionamento progetto. riprova.",
        "../public/progetti.php"
    );
}


// 4. È stato selezionato un commento valido
if (!isset($_POST['id_commento'])) {
    redirect(
        false,
        "Errore eliminazione commento. Riprova.",
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// === ACTION ===
// Elimino il commento
try {
    $in = [
        'p_id' => $_POST['id_commento'],
        'p_email' => $_SESSION['email'],
        'p_nome_progetto' => $_POST['nome_progetto']
    ];

    sp_invoke('sp_commento_delete', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore nell'eliminazione del commento: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
    );
}

// Success, redirect alla pagina del progetto
redirect(
    true,
    "Commento eliminato correttamente.",
    "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
);