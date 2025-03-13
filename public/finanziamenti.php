<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// Recupero i finanziamenti effettuati dall'utente
try {
    $in = ['p_email' => $_SESSION['email']];
    $finanziamenti_utente = sp_invoke('sp_finanziamento_selectAllByUtente', $in);
} catch (PDOException $ex) {
    $finanziamenti_utente = [];
    $finUtenteError = "Errore nel recupero dei finanziamenti: " . $ex->errorInfo[2];
}

// Recupero i dettagli delle reward per ogni finanziamento
if (!empty($finanziamenti_utente)) {
    foreach ($finanziamenti_utente as $key => &$finanziamento) {
        try {
            // Recupero i dettagli del progetto per ottenere il nome del creatore
            $in_progetto = ['p_nome' => $finanziamento['nome_progetto']];
            $progetto = sp_invoke('sp_progetto_select', $in_progetto)[0] ?? null;
            if ($progetto) {
                $finanziamento['email_creatore'] = $progetto['email_creatore'];
                $finanziamento['progetto_stato'] = $progetto['stato'];
                $finanziamento['progetto_budget'] = $progetto['budget'];
            }

            // Recupero i dettagli della reward
            $rewards = sp_invoke('sp_reward_selectAllByProgetto', ['p_nome_progetto' => $finanziamento['nome_progetto']]);

            // Cerca la reward con il codice corrispondente
            foreach ($rewards as $reward) {
                if ($reward['codice'] === $finanziamento['codice_reward']) {
                    $finanziamento['reward_descrizione'] = $reward['descrizione'];
                    $finanziamento['reward_foto'] = $reward['foto'];
                    break;
                }
            }
        } catch (PDOException $ex) {
            $finanziamento['reward_descrizione'] = 'Errore nel recupero della reward: ' . $ex->errorInfo[2];
            $finanziamento['reward_foto'] = null;
        }
    }
    // Importante: libera il riferimento dopo il ciclo foreach
    unset($finanziamento);
}

// Se l'utente è un creatore, recupero anche i finanziamenti ricevuti dai suoi progetti
$finanziamenti_ricevuti = [];
if ($_SESSION['is_creatore']) {
    try {
        $in = ['p_email_creatore' => $_SESSION['email']];
        $finanziamenti_ricevuti = sp_invoke('sp_finanziamento_selectAllByProgetto', $in);

        foreach ($finanziamenti_ricevuti as &$finanziamento) {
            // Recupero nickname del finanziatore
            $in_utente = ['p_email' => $finanziamento['email_utente']];
            $utente = sp_invoke('sp_utente_select', $in_utente)[0] ?? null;
            $finanziamento['finanziatore_nickname'] = $utente['nickname'] ?? 'Utente sconosciuto';

            // Recupero dettagli della reward
            if (!isset($finanziamento['reward_descrizione']) || !isset($finanziamento['reward_foto'])) {
                $in_reward = ['p_nome_progetto' => $finanziamento['nome_progetto']];
                $rewards = sp_invoke('sp_reward_selectAllByProgetto', $in_reward);
                foreach ($rewards as $reward) {
                    if ($reward['codice'] === $finanziamento['codice_reward']) {
                        $finanziamento['reward_descrizione'] = $reward['descrizione'];
                        $finanziamento['reward_foto'] = $reward['foto'];
                        break;
                    }
                }
            }

            // Recupero dettagli del progetto
            if (!isset($finanziamento['progetto_stato']) || !isset($finanziamento['progetto_budget'])) {
                $in_progetto = ['p_nome' => $finanziamento['nome_progetto']];
                $progetto = sp_invoke('sp_progetto_select', $in_progetto)[0] ?? null;
                if ($progetto) {
                    $finanziamento['progetto_stato'] = $progetto['stato'];
                    $finanziamento['progetto_budget'] = $progetto['budget'];
                }
            }
        }
        // Importante: libera il riferimento dopo il ciclo foreach
        unset($finanziamento);
    } catch (PDOException $ex) {
        $finRicevutiError = "Errore nel recupero dei finanziamenti ricevuti: " . $ex->errorInfo[2];
    }
}

// Calcolo somma totale finanziamenti effettuati dall'utente
$totale_finanziamenti_effettuati = 0;
foreach ($finanziamenti_utente as $finanziamento) {
    $totale_finanziamenti_effettuati += $finanziamento['importo'];
}

// Calcolo somma totale finanziamenti ricevuti dai progetti dell'utente (solo per creatori)
$totale_finanziamenti_ricevuti = 0;
foreach ($finanziamenti_ricevuti as $finanziamento) {
    $totale_finanziamenti_ricevuti += $finanziamento['importo'];
}
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- Messaggio di successo/errore post-azione -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <h1 class="mb-4">Finanziamenti</h1>

    <!-- Sezione per utenti creatori - Finanziamenti ricevuti -->
    <?php if ($_SESSION['is_creatore']): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Finanziamenti Ricevuti</h4>
                    <h5 class="mb-0"><span class="badge bg-success">Totale: <?php echo number_format($totale_finanziamenti_ricevuti, 2); ?>€</span></h5>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($finRicevutiError)): ?>
                    <p class="text-danger"><?php echo htmlspecialchars($finRicevutiError); ?></p>
                <?php elseif (empty($finanziamenti_ricevuti)): ?>
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
                            <?php foreach ($finanziamenti_ricevuti as $finanziamento): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($finanziamento['data']); ?></td>
                                    <td>
                                        <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($finanziamento['nome_progetto']); ?>">
                                            <?php echo htmlspecialchars($finanziamento['nome_progetto']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($finanziamento['finanziatore_nickname'] ?? 'N/A'); ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($finanziamento['email_utente']); ?></small>
                                    </td>
                                    <td class="fw-bold"><?php echo htmlspecialchars(number_format($finanziamento['importo'], 2)); ?>€</td>
                                    <td>
                                        <?php if (isset($finanziamento['reward_foto'])): ?>
                                            <?php $base64 = base64_encode($finanziamento['reward_foto']); ?>
                                            <div class="d-flex align-items-center">
                                                <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                     class="img-thumbnail me-2"
                                                     alt="Reward"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                                    <?php if (isset($finanziamento['reward_descrizione'])): ?>
                                                        <small class="d-block"><?php echo htmlspecialchars($finanziamento['reward_descrizione']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <strong><?php echo htmlspecialchars($finanziamento['codice_reward']); ?></strong>
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

    <!-- Sezione per tutti gli utenti - Finanziamenti effettuati -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Finanziamenti Effettuati</h4>
                <h5 class="mb-0"><span class="badge bg-success">Totale: <?php echo number_format($totale_finanziamenti_effettuati, 2); ?>€</span></h5>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($finUtenteError)): ?>
                <p class="text-danger"><?php echo htmlspecialchars($finUtenteError); ?></p>
            <?php elseif (empty($finanziamenti_utente)): ?>
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
                        <?php foreach ($finanziamenti_utente as $finanziamento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($finanziamento['data']); ?></td>
                                <td>
                                    <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($finanziamento['nome_progetto']); ?>">
                                        <?php echo htmlspecialchars($finanziamento['nome_progetto']); ?>
                                    </a>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars(number_format($finanziamento['importo'], 2)); ?>€</td>
                                <td>
                                    <?php if (isset($finanziamento['reward_foto'])): ?>
                                        <?php $base64 = base64_encode($finanziamento['reward_foto']); ?>
                                        <div class="d-flex align-items-center">
                                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                 class="img-thumbnail me-2"
                                                 alt="Reward"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($finanziamento['codice_reward']); ?></strong>
                                                <?php if (isset($finanziamento['reward_descrizione'])): ?>
                                                    <small class="d-block"><?php echo htmlspecialchars($finanziamento['reward_descrizione']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <strong><?php echo htmlspecialchars($finanziamento['codice_reward']); ?></strong>
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