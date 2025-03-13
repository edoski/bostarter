<?php
// Se il progetto è di tipo HARDWARE, recupero il costo delle componenti
if ($progetto['tipo'] === 'HARDWARE') {
    try {
        $in = ['p_nome_progetto' => $_GET['nome']];
        $out = ['p_costo_totale_out' => 0];
        sp_invoke('sp_util_progetto_componenti_costo', $in, $out);
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero del costo delle componenti: " . $ex->errorInfo[2],
            '../public/progetti.php'
        );
    }
}

// Recupero il totale dei finanziamenti e calcolo la percentuale
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $totalFin = sp_invoke('sp_finanziamento_selectSumByProgetto', $in)[0]['totale_finanziamenti'] ?? 0;
    $progetto['tot_finanziamento'] = $totalFin;
    $progetto['percentuale'] = ($progetto['budget'] > 0) ? ($totalFin / $progetto['budget']) * 100 : 0;
} catch (PDOException $ex) {
    $progetto['tot_finanziamento'] = 0;
    $progetto['percentuale'] = 0;
}
?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Aggiorna Budget</h3>
    </div>
    <div class="card-body">
        <!-- Dettagli Finanziamenti -->
        <div class="bg-secondary-subtle p-1 rounded text-center mb-3">
            <p class="fs-4">
                <strong>Budget Attuale:</strong> <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
            </p>
            <?php if ($progetto['tipo'] === 'HARDWARE'): ?>
                <p class="fs-5">
                    <strong>Costo Componenti:</strong> <?php echo htmlspecialchars(number_format($out['p_costo_totale_out'], 2)); ?>€
                </p>
            <?php endif; ?>
        </div>
        <div class="d-flex w-100 fw-bold justify-content-center fs-5 mb-3">
            <?php echo round($progetto['percentuale'], 2); ?>% Finanziato
        </div>
        <div class="progress mb-3 position-relative" style="height: 40px;">
            <div class="progress-bar fw-bold bg-success"
                 style="width: <?php echo round($progetto['percentuale'], 2); ?>%; height: 100%;">
            </div>
            <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black fs-6">
                <?php echo htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                / <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
            </div>
        </div>
        <!-- Form per aggiornare il budget -->
        <form action="../actions/progetto_budget_update.php" method="post">
            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($progetto['tipo']); ?>">
            <input type="hidden" name="attr" value="budget">
            <div class="form-group">
                <label for="budget" class="fw-bold">Nuovo Budget (€)</label>
                <p class="small text-muted">
                    Il nuovo budget non può essere inferiore al costo delle componenti
                    (<?php echo htmlspecialchars(number_format($out['p_costo_totale_out'] ?? 0, 2)); ?>€)
                </p>
                <input type="number" step="0.01" min="0.01" class="form-control" id="budget"
                       name="budget" required
                       placeholder="<?php echo htmlspecialchars($progetto['budget']); ?>">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Aggiorna Budget</button>
        </form>
    </div>
</div>