<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];
$is_creatore = $_SESSION['is_creatore'];

// === CONTEXT ===
$context = [
    'collection' => 'FINANZIAMENTO',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('home')
];
$pipeline = new EventPipeline($context);

// === DATA ===
// RECUPERO FINANZIAMENTI EFFETTUATI DALL'UTENTE
$finanziamenti_effettuati = $pipeline->fetch_all('sp_finanziamento_selectAllByUtente', ['p_email' => $email]);

// RECUPERO DETTAGLI PER OGNI FINANZIAMENTO
$totale_finanziamenti_effettuati = 0;
if (!empty($finanziamenti_effettuati['data'])) {
    foreach ($finanziamenti_effettuati['data'] as $key => &$finanziamento) {
        // DETTAGLI DEL PROGETTO
        $progetto = $pipeline->fetch('sp_progetto_select', ['p_nome' => $finanziamento['nome_progetto']]);
        $finanziamento['email_creatore'] = $progetto['email_creatore'];
        $finanziamento['progetto_stato'] = $progetto['stato'];
        $finanziamento['progetto_budget'] = $progetto['budget'];

        // DETTAGLI DELLA REWARD
        $rewards = $pipeline->fetch_all('sp_reward_selectAllByProgetto', ['p_nome_progetto' => $finanziamento['nome_progetto']]);
        foreach ($rewards['data'] as $reward) {
            if ($reward['codice'] === $finanziamento['codice_reward']) {
                $finanziamento['reward_descrizione'] = $reward['descrizione'];
                $finanziamento['reward_foto'] = $reward['foto'];
                break;
            }
        }

        // SOMMA FINANZIAMENTI EFFETTUATI DALL'UTENTE
        $totale_finanziamenti_effettuati += $finanziamento['importo'];
    }
    // IMPORTANTE: LIBERA IL RIFERIMENTO DOPO IL CICLO FOREACH
    unset($finanziamento);
}

// SE L'UTENTE È UN CREATORE, RECUPERO FINANZIAMENTI RICEVUTI
if ($is_creatore) {
    $finanziamenti_ricevuti = $pipeline->fetch_all('sp_finanziamento_selectAllByProgetto', ['p_email_creatore' => $email]);

    // RECUPERO DETTAGLI PER OGNI FINANZIAMENTO
    $totale_finanziamenti_ricevuti = 0;
    if (!empty($finanziamenti_ricevuti['data'])) {
        foreach ($finanziamenti_ricevuti['data'] as &$finanziamento) {
            // DETTAGLI DEL FINANZIATORE
            $utente = $pipeline->fetch('sp_utente_select', ['p_email' => $finanziamento['email_utente']]);
            $finanziamento['finanziatore_nickname'] = $utente['nickname'] ?? 'Utente sconosciuto';

            // DETTAGLI DEL PROGETTO
            $progetto = $pipeline->fetch('sp_progetto_select', ['p_nome' => $finanziamento['nome_progetto']]);
            $finanziamento['progetto_stato'] = $progetto['stato'];
            $finanziamento['progetto_budget'] = $progetto['budget'];

            // DETTAGLI DELLA REWARD
            $rewards = $pipeline->fetch_all('sp_reward_selectAllByProgetto', ['p_nome_progetto' => $finanziamento['nome_progetto']]);
            foreach ($rewards['data'] as $reward) {
                if ($reward['codice'] === $finanziamento['codice_reward']) {
                    $finanziamento['reward_descrizione'] = $reward['descrizione'];
                    $finanziamento['reward_foto'] = $reward['foto'];
                    break;
                }
            }

            // SOMMA FINANZIAMENTI RICEVUTI DAI PROGETTI (SE CREATORE)
            $totale_finanziamenti_ricevuti += $finanziamento['importo'];
        }
        // IMPORTANTE: LIBERA IL RIFERIMENTO DOPO IL CICLO FOREACH
        unset($finanziamento);
    }
}
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- ALERT -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- TITLE -->
    <h1 class="mb-4">Finanziamenti</h1>

    <!-- FINANZIAMENTI RICEVUTI (CREATORI) -->
    <?php if ($is_creatore): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Finanziamenti Ricevuti</h4>
                    <h5 class="mb-0"><span class="badge bg-success">Totale: <?= number_format($totale_finanziamenti_ricevuti, 2); ?>€</span></h5>
                </div>
            </div>
            <div class="card-body">
                <?php if ($finanziamenti_ricevuti['failed']): ?>
                    <p class="text-danger">C'è stato un errore nel recupero dei finanziamenti ricevuti.</p>
                <?php elseif (empty($finanziamenti_ricevuti['data'])): ?>
                    <p>I tuoi progetti non hanno ancora ricevuto finanziamenti.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Data</th>
                                <th>Progetto</th>
                                <th>Finanziatore</th>
                                <th>Importo</th>
                                <th>Reward</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($finanziamenti_ricevuti['data'] as $finanziamento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($finanziamento['data']); ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $finanziamento['nome_progetto']])); ?>">
                                            <?= htmlspecialchars($finanziamento['nome_progetto']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($finanziamento['finanziatore_nickname'] ?? 'N/A'); ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($finanziamento['email_utente']); ?></small>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars(number_format($finanziamento['importo'], 2)); ?>€</td>
                                    <td>
                                        <?php if (isset($finanziamento['reward_foto'])): ?>
                                            <?php $base64 = base64_encode($finanziamento['reward_foto']); ?>
                                            <div class="d-flex align-items-center">
                                                <img src="data:image/jpeg;base64,<?= $base64; ?>"
                                                     class="img-thumbnail me-2"
                                                     alt="Reward"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <strong><?= htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                                    <?php if (isset($finanziamento['reward_descrizione'])): ?>
                                                        <small class="d-block"><?= htmlspecialchars($finanziamento['reward_descrizione']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <strong><?= htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- FINANZIAMENTI EFFETTUATI (ALL) -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Finanziamenti Effettuati</h4>
                <h5 class="mb-0"><span class="badge bg-success">Totale: <?= number_format($totale_finanziamenti_effettuati, 2); ?>€</span></h5>
            </div>
        </div>
        <div class="card-body">
            <?php if ($finanziamenti_effettuati['failed']): ?>
                <p class="text-danger">C'è stato un errore nel recupero dei finanziamenti effettuati.</p>
            <?php elseif (empty($finanziamenti_effettuati['data'])): ?>
                <p>Non hai ancora effettuato finanziamenti.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Progetto</th>
                            <th>Importo</th>
                            <th>Reward</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($finanziamenti_effettuati['data'] as $finanziamento): ?>
                            <tr>
                                <td><?= htmlspecialchars($finanziamento['data']); ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $finanziamento['nome_progetto']])); ?>">
                                        <?= htmlspecialchars($finanziamento['nome_progetto']); ?>
                                    </a>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars(number_format($finanziamento['importo'], 2)); ?>€</td>
                                <td>
                                    <?php if (isset($finanziamento['reward_foto'])): ?>
                                        <?php $base64 = base64_encode($finanziamento['reward_foto']); ?>
                                        <div class="d-flex align-items-center">
                                            <img src="data:image/jpeg;base64,<?= $base64; ?>"
                                                 class="img-thumbnail me-2"
                                                 alt="Reward"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <strong><?= htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                                <?php if (isset($finanziamento['reward_descrizione'])): ?>
                                                    <small class="d-block"><?= htmlspecialchars($finanziamento['reward_descrizione']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <strong><?= htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>