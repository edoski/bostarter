<?php
/**
 * COMPONENT: progetto_aggiorna_componenti (PARENT: progetto_aggiorna)
 *
 * ACTIONS: componente_insert, componente_update, componente_delete
 *
 * PURPOSE:
 * - Gestisce l'interfaccia principale per la gestione dei componenti di un progetto hardware.
 * - Coordina la visualizzazione dei componenti per creare, modificare ed eliminare componenti.
 * - Recupera e organizza i dati dei componenti esistenti.
 */

// === DATA ===
// RECUPERO COMPONENTI DEL PROGETTO
$in = ['p_nome_progetto' => $_GET['nome']];
$componenti = $pipeline->fetch_all('sp_componente_selectAllByProgetto', $in);

// DETERMINO SE CREANDO/AGGIORNANDO UN COMPONENTE
$componente_selezionato = $_GET['componente'] ?? '';
$nuovo_componente = empty($componente_selezionato);

// RECUPERO BUDGET DEL PROGETTO
$in = ['p_nome' => $_GET['nome']];
$progetto = $pipeline->fetch('sp_progetto_select', $in);

// SE MODIFICANDO, RECUPERO DATI COMPONENTE ATTUALE
if (!$nuovo_componente) {
    foreach ($componenti['data'] as $componente) {
        if ($componente['nome_componente'] == $componente_selezionato) {
            $componente_attuale = $componente;
            break;
        }
    }

    $pipeline->check(
        !isset($componente_attuale),
        "Componente non trovato.",
        generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $_GET['nome']])
    );
}
?>

<div class="row">
    <!-- COLONNA SINISTRA -->
    <?php require '../components/componenti_esistenti.php'; ?>
    <!-- COLONNA DESTRA -->
    <?php
    if ($nuovo_componente) require '../components/componente_nuovo.php';
    else require '../components/componente_modifica.php';
    ?>
</div>