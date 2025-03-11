<?php
// === DATABASE ===
try {
    // Recupero tutti i profili del progetto
    $in = ['p_nome_progetto' => $_GET['nome']];
    $result = sp_invoke('sp_profilo_selectAllByProgetto', $in);

    // Inizializza array dei profili
    $profili = [];

    // Organizzo i risultati per profilo
    foreach ($result as $row) {
        $profili[$row['nome_profilo']] = $profili[$row['nome_profilo']] ?? [];
        $profili[$row['nome_profilo']][] = [
            'competenza' => $row['competenza'] ?? '',
            'livello' => $row['livello_richiesto'] ?? ''
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

// Controlla se stiamo modificando un profilo specifico o una competenza
$profiloSelezionato = $_GET['profilo'] ?? '';
$competenzaSelezionata = $_GET['competenza'] ?? '';
$livelloSelezionato = $_GET['livello'] ?? 0;
$competenzeSelezionate = [];
$competenzeDisponibili = [];

// Se stiamo modificando un profilo
if (!empty($profiloSelezionato)) {
    if (isset($profili[$profiloSelezionato])) {
        $competenzeSelezionate = $profili[$profiloSelezionato];

        // Ottieni le competenze non ancora associate a questo profilo
        try {
            $in = [
                'p_nome_profilo' => $profiloSelezionato,
                'p_nome_progetto' => $_GET['nome']
            ];
            $competenzeDisponibili = sp_invoke('sp_skill_profilo_selectDiff', $in);
        } catch (PDOException $ex) {
            // In caso di errore, utilizza tutte le competenze
            $competenzeDisponibili = $competenzeGlobali;
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
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <?php if (empty($profiloSelezionato)) echo "Nuovo Profilo";
                    else if (!empty($competenzaSelezionata)) echo "Modifica Competenza: " . htmlspecialchars($competenzaSelezionata);
                    else echo "Modifica: " . htmlspecialchars($profiloSelezionato); ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($competenzaSelezionata)): ?>
                    <!-- Form per modificare una competenza specifica -->
                    <?php require '../components/profilo_competenza_modifica.php'; ?>
                <?php elseif (empty($profiloSelezionato)): ?>
                    <!-- Form per creare un nuovo profilo -->
                    <form action="../actions/profilo_insert.php" method="post">
                        <input type="hidden" name="nome_progetto"
                               value="<?php echo htmlspecialchars($_GET['nome']); ?>">

                        <div class="mb-3">
                            <label for="nome_profilo" class="form-label fw-bold">Nome Profilo</label>
                            <input type="text" class="form-control" id="nome_profilo" name="nome_profilo" required
                                   placeholder="Es. Frontend Developer">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Crea Profilo</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Form per modificare un profilo esistente -->
                    <?php require '../components/profilo_modifica_esistente.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>