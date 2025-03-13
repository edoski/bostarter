<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è un creatore
checkCreatore();

// 3. Le variabili POST sono state impostate correttamente
checkSetVars(['nome', 'descrizione', 'budget', 'data_limite', 'tipo']);

// 4. Recupero e valido i dati
$nome = trim($_POST['nome']);
$descrizione = trim($_POST['descrizione']);
$budget = floatval($_POST['budget']);
$data_limite = trim($_POST['data_limite']);
$tipo = $_POST['tipo'];

// Validazione
if (empty($nome) || empty($descrizione) || $budget < 0.01 || empty($data_limite) || empty($tipo)) {
    redirect(
        false,
        "Tutti i campi sono obbligatori e il budget deve essere maggiore di 0.01.",
        "../public/progetto_crea.php"
    );
}

if ($data_limite <= date('Y-m-d')) {
    redirect(
        false,
        "La data limite deve essere futura.",
        "../public/progetto_crea.php"
    );
}

if ($tipo !== 'software' && $tipo !== 'hardware') {
    redirect(
        false,
        "Il tipo di progetto deve essere software o hardware.",
        "../public/progetto_crea.php"
    );
}

// === ACTION ===
// Inserimento del progetto
try {
    $in = [
        'p_nome' => $nome,
        'p_email_creatore' => $_SESSION['email'],
        'p_descrizione' => $descrizione,
        'p_budget' => $budget,
        'p_data_limite' => $data_limite,
        'p_tipo' => $tipo
    ];

    sp_invoke('sp_progetto_insert', $in);

    // Inserisco la reward RWD_Default
    $defaultRewardPath = __DIR__ . '/../img/RWD_Default.jpg';
    $defaultRewardCreated = false;

    if (file_exists($defaultRewardPath)) {
        $imageData = file_get_contents($defaultRewardPath);

        $in_reward = [
            'p_codice' => 'RWD_Default',
            'p_nome_progetto' => $nome,
            'p_email_creatore' => $_SESSION['email'],
            'p_descrizione' => 'Commodo ipsum dolor dolore ullamco aliqua dolor aliqua.',
            'p_foto' => $imageData,
            'p_min_importo' => 0.01
        ];

        sp_invoke('sp_reward_insert', $in_reward);
        $defaultRewardCreated = true;
    } else {
        // Log error but continue
        error_log("RWD_Default.jpg not found at $defaultRewardPath");
    }

    // Redirect con messaggio appropriato
    if ($defaultRewardCreated) {
        redirect(
            true,
            "Progetto creato con successo. Per ciascuna sezione, clicca sul bottone giallo 'Modifica' per iniziare a personalizzare il progetto.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nome)
        );
    }
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante la creazione del progetto: " . $ex->errorInfo[2],
        "../public/progetto_crea.php"
    );
}