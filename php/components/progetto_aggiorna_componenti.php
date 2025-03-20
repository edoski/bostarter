<?php
// === DATA ===
try {
    // Recupero tutti i componenti del progetto
    $in = ['p_nome_progetto' => $_GET['nome']];
    $componenti = sp_invoke('sp_componente_selectAllByProgetto', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei componenti: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_GET['nome'])
    );
}

// Controlla se stiamo modificando un componente specifico
$componente_selezionato = $_GET['componente'] ?? '';
$nuovo_componente = empty($componente_selezionato);

// Recupera il budget attuale del progetto
try {
    $in = ['p_nome' => $_GET['nome']];
    $progetto = sp_invoke('sp_progetto_select', $in)[0];
    $budget_progetto = $progetto['budget'];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del budget del progetto: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_GET['nome'])
    );
}

// Se stiamo modificando un componente esistente, recuperare i suoi dettagli
if (!$nuovo_componente) {
    try {
        foreach ($componenti as $componente) {
            if ($componente['nome_componente'] == $componente_selezionato) {
                $componenteCorrente = $componente;
                break;
            }
        }

        if (!isset($componenteCorrente)) {
            redirect(
                false,
                "Componente non trovato.",
                "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_GET['nome'])
            );
        }
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del componente: " . $ex->errorInfo[2],
            "../public/progetto_aggiorna.php?attr=componenti&nome=" . urlencode($_GET['nome'])
        );
    }
}
?>

<div class="row">
    <!-- Colonna sinistra: Lista componenti esistenti -->
    <?php require '../components/componenti_esistenti.php'; ?>

    <!-- Colonna destra: Area di modifica o creazione -->
    <?php require '../components/componente_modifica.php'; ?>
</div>