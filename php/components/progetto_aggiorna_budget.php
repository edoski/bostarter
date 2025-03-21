<?php
// === DATA ===
$in = ['p_nome_progetto' => $_GET['nome']];
$is_hardware = $progetto['tipo'] === 'HARDWARE';

// SE HARDWARE, RECUPERO IL COSTO DEI COMPONENTI
if ($is_hardware) $costo_componenti = $pipeline->fetch('sp_util_progetto_componenti_costo', $in)['costo_totale'];

// RECUPERO SOMMA FINANZIAMENTI E CALCOLO PERCENTUALE
$progetto['tot_finanziamento'] = $pipeline->fetch('sp_finanziamento_selectSumByProgetto', $in)['totale_finanziamenti'];
$progetto['percentuale'] = ($progetto['tot_finanziamento'] / $progetto['budget']) * 100;
?>

<!-- === PAGE === -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Aggiorna Budget</h3>
    </div>
    <div class="card-body">
        <div class="bg-secondary-subtle p-1 rounded text-center mb-3">
            <p class="fs-4">
                <strong>Budget Attuale:</strong> <?= htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
            </p>
            <?php if ($is_hardware): ?>
                <p class="fs-5">
                    <strong>Costo Componenti:</strong> <?= htmlspecialchars(number_format($costo_componenti, 2)); ?>€
                </p>
            <?php endif; ?>
        </div>
        <div class="d-flex w-100 fw-bold justify-content-center fs-5 mb-3">
            <?= round($progetto['percentuale'], 2); ?>% Finanziato
        </div>
        <div class="progress mb-3 position-relative" style="height: 40px;">
            <div class="progress-bar fw-bold bg-success"
                 style="width: <?= round($progetto['percentuale'], 2); ?>%; height: 100%;">
            </div>
            <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black fs-6">
                <?= htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                / <?= htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
            </div>
        </div>
        <!-- AGGIORNA BUDGET -->
        <form action="../actions/progetto_budget_update.php" method="post">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($_GET['nome']); ?>">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($progetto['tipo']); ?>">
            <input type="hidden" name="attr" value="budget">
            <div class="form-group">
                <label for="budget" class="fw-bold">Nuovo Budget (€)</label>
                <?php if ($is_hardware): ?>
                <p class="small text-muted">
                    Il nuovo budget non può essere inferiore al costo delle componenti
                    (<?= htmlspecialchars(number_format($costo_componenti, 2)); ?>€)
                </p>
                <?php endif; ?>
                <input type="number" step="0.01" min="0.01" class="form-control" id="budget"
                       name="budget" required
                       placeholder="<?= htmlspecialchars($progetto['budget']); ?>">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Aggiorna Budget</button>
        </form>
    </div>
</div>