<?php
// === VARIABLES ===
$profilo_selezionato = $_GET['profilo'] ?? '';
$competenza_selezionata = $_GET['competenza'] ?? '';
$livello_selezionato = $_GET['livello'] ?? 0;

// === DATA ===
// PROFILI E RELATIVE COMPETENZE
$profili = $pipeline->fetch_all('sp_profilo_selectAllByProgetto', ['p_nome_progetto' => $_GET['nome']]);

// ORGANIZZAZIONE DATI PER PROFILO
$profilo_data = [];
foreach ($profili['data'] as $row) {
    $nome = $row['nome_profilo'];
    if (!isset($profilo_data[$nome])) {
        $profilo_data[$nome] = [];
    }

    if (!empty($row['competenza'])) {
        $profilo_data[$nome][] = [
            'competenza' => $row['competenza'],
            'livello' => $row['livello_richiesto']
        ];
    }
}
$profili = $profilo_data;

// COMPETENZE GLOBALI
$competenze_globali = $pipeline->fetch_all('sp_skill_selectAll');

// SE MODIFICA, RECUPERO COMPETENZA SELEZIONATA
if (!empty($profilo_selezionato)) {
    if (isset($profili[$profilo_selezionato])) {
        // Filtra le competenze per rimuovere elementi vuoti
        $competenze_selezionate = array_filter($profili[$profilo_selezionato], function ($item) {
            return !empty($item['competenza']);
        });

        // COMPETENZE NON ANCORA ASSEGNATE AL PROFILO
        $in = [
            'p_nome_profilo' => $profilo_selezionato,
            'p_nome_progetto' => $_GET['nome']
        ];
        $competenze_disponibili = $pipeline->fetch_all('sp_skill_profilo_selectDiff', $in)['data'];
    }
} else {
    // SE NUOVO PROFILO, TUTTE LE COMPETENZE SONO DISPONIBILI
    $competenze_disponibili = $competenze_globali;
}
?>

<div class="row">
    <!-- COLONNA SINISTRA -->
    <?php require '../components/profili_esistenti.php'; ?>
    <!-- COLONNA DESTRA -->
    <?php require '../components/profilo_modifica.php'; ?>
</div>