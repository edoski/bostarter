<?php
// === DATABASE ===
try {
    // Recupero tutti i profili del progetto
    $in = ['p_nome_progetto' => $_GET['nome']];
    $result = sp_invoke('sp_profilo_skill_selectAllByProgetto', $in);

    // Organizzo i risultati per profilo
    foreach ($result as $row) {
        $profili[$row['nome_profilo']][] = [
            'competenza' => $row['competenza'],
            'livello' => $row['livello_richiesto']
        ];
    }

    // Recupero tutte le competenze disponibili
    $competenzeGlobali = sp_invoke('sp_skill_selectAll');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei profili: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_GET['nome'])
    );
}

// Check if we're editing a specific profile or skill
$profiloSelezionato = $_GET['profilo'] ?? '';
$competenzaSelezionata = $_GET['competenza'] ?? '';
$livelloSelezionato = $_GET['livello'] ?? 0;
$competenzeSelezionate = [];
$competenzeDisponibili = [];

// If editing a profile
if (!empty($profiloSelezionato)) {
    if (isset($profili[$profiloSelezionato])) {
        $competenzeSelezionate = $profili[$profiloSelezionato];

        // Get available skills not already in this profile
        try {
            $in = [
                'p_nome_profilo' => $profiloSelezionato,
                'p_nome_progetto' => $_GET['nome']
            ];
            $competenzeDisponibili = sp_invoke('sp_skill_profilo_selectDiff', $in);
        } catch (PDOException $ex) {
            // On error, fallback to all skills
            $competenzeDisponibili = $competenzeGlobali;
        }
    }
}
?>

<div class="row">
    <!-- Colonna sinistra: Lista profili esistenti -->
    <?php require '../components/profili_esistenti.php'; ?>
    <!-- Colonna destra: Area di modifica o creazione -->
    <?php require '../components/profilo_modifica.php'; ?>
</div>