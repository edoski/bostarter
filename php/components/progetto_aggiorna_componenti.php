<?php
// === DATA ===
// Recupero tutti i componenti del progetto
$in = ['p_nome_progetto' => $_GET['nome']];
$componenti = $pipeline->fetch_all('sp_componente_selectAllByProgetto', $in);

// Controlla se stiamo modificando un componente specifico
$componente_selezionato = $_GET['componente'] ?? '';
$nuovo_componente = empty($componente_selezionato);

// Recupera il budget attuale del progetto
$in = ['p_nome' => $_GET['nome']];
$progetto = $pipeline->fetch('sp_progetto_select', $in);

// Se stiamo modificando un componente esistente, recuperare i suoi dettagli
if (!$nuovo_componente) {
    try {
        foreach ($componenti['data'] as $componente) {
            if ($componente['nome_componente'] == $componente_selezionato) {
                $componente_attuale = $componente;
                break;
            }
        }

        if (!isset($componente_attuale)) {
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