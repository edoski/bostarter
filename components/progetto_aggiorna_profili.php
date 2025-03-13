<?php
// === DATABASE ===
try {
    // Recupero tutti i profili del progetto
    $in = ['p_nome_progetto' => $_GET['nome']];
    $result = sp_invoke('sp_profilo_selectAllByProgetto', $in);

    // Organizzo i risultati per profilo
    foreach ($result as $row) {
        $profili[$row['nome_profilo']][] = [
            'competenza' => $row['competenza'] ?? '',
            'livello' => $row['livello_richiesto'] ?? ''
        ];
    }

    // Rimuovo le entry vuote (accade quando viene appena creato un profilo, prima di aggiungere competenze)
    foreach ($profili as $profiloName => &$competenze) {
        $competenze = array_filter($competenze, function($item) {
            return !empty($item['competenza']);
        });
    }
    unset($competenze); // Important: unset reference per evitare side effects

    // Recupero tutte le competenze disponibili
    $competenzeGlobali = sp_invoke('sp_skill_selectAll');
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei profili: " . $ex->errorInfo[2],
        "../public/progetto_dettagli.php?nome=" . urlencode($_GET['nome'])
    );
}

// Controlla se stiamo modificando un profilo specifico o una competenza
$profiloSelezionato = $_GET['profilo'] ?? '';
$competenzaSelezionata = $_GET['competenza'] ?? '';
$livelloSelezionato = $_GET['livello'] ?? 0;
$competenzeSelezionate = [];
$competenzeDisponibili = [];

// Se stiamo modificando un profilo specifico
if (!empty($profiloSelezionato)) {
    if (isset($profili[$profiloSelezionato])) {
        // Filtra le competenze per rimuovere elementi vuoti
        $competenzeSelezionate = array_filter($profili[$profiloSelezionato], function($item) {
            return !empty($item['competenza']);
        });

        // Ottieni le competenze non ancora associate a questo profilo
        try {
            $in = [
                'p_nome_profilo' => $profiloSelezionato,
                'p_nome_progetto' => $_GET['nome']
            ];
            $competenzeDisponibili = sp_invoke('sp_skill_profilo_selectDiff', $in);
        } catch (PDOException $ex) {
            redirect(
                false,
                "Errore durante il recupero delle competenze disponibili: " . $ex->errorInfo[2],
                "../public/progetto_aggiorna.php?attr=profili&nome=" . urlencode($_GET['nome'])
            );
        }
    }
} else {
    // Se stiamo creando un nuovo profilo, tutte le competenze sono disponibili
    $competenzeDisponibili = $competenzeGlobali;
}
?>

<div class="row">
    <!-- Colonna sinistra: Lista profili esistenti -->
    <?php require '../components/profili_esistenti.php'; ?>

    <!-- Colonna destra: Area di modifica o creazione -->
    <?php require '../components/profilo_modifica.php'; ?>
</div>