<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// I dati dell'utente sono già presenti in $_SESSION
// Se l'utente è un creatore, recupero i dati relativi ad esso
if ($_SESSION['is_creatore']) {
    try {
        $in = ['p_email' => $_SESSION['email']];

        // Recupero i progetti creati dall'utente
        $progetti = sp_invoke('sp_progetto_selectByCreatore', $in);

        // Recupero la sua affidabilità
        $_SESSION['affidabilita'] = sp_invoke('sp_util_get_creatore_affidabilita', $in)[0]['affidabilita'];

        // Recupero il suo nr_progetti
        $_SESSION['nr_progetti'] = sp_invoke('sp_util_get_creatore_nr_progetti', $in)[0]['nr_progetti'];
    } catch (PDOException $ex) {
        $progetti = [];
        $progettiError = "Errore nel recupero dei progetti: " . $ex->errorInfo[2];
    }
}

// Recupero i finanziamenti effettuati dall'utente
try {
    $in = ['p_email' => $_SESSION['email']];
    $finanziamenti = sp_invoke('sp_finanziamento_selectAllByUtente', $in);

    // Calcolo il totale dei finanziamenti
    $totale_finanziamenti = 0;
    foreach ($finanziamenti as $finanziamento) {
        $totale_finanziamenti += $finanziamento['importo'];
    }
} catch (PDOException $ex) {
    $finanziamenti = [];
    $finError = "Errore nel recupero dei finanziamenti: " . $ex->errorInfo[2];
}

// Recupero le skill del curriculum dell'utente
try {
    $in = ['p_email' => $_SESSION['email']];
    $skills = sp_invoke('sp_skill_curriculum_selectAll', $in);
} catch (PDOException $ex) {
    $skills = [];
    $skillsError = "Errore nel recupero delle skills: " . $ex->errorInfo[2];
}
?>

<?php require '../components/header.php'; ?>
<div class="container flex-grow-1 my-4">
    <!-- Messaggio di successo/errore post-azione -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- Top row -->
    <div class="row g-4">
        <!-- User Info (Bio) Card -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Il tuo profilo</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Dati personali -->
                        <div class="col-12 col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Dati personali</h5>
                                    <div>
                                        <?php if (!$_SESSION['is_creatore'] && !$_SESSION['is_admin']): ?>
                                            <span class="badge bg-light text-secondary fs-6">Utente</span>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['is_creatore']): ?>
                                            <span class="badge bg-primary fs-6">Creatore</span>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['is_admin']): ?>
                                            <span class="badge bg-danger fs-6">Admin</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                            <p><strong>Anno di nascita:</strong> <?php echo htmlspecialchars($_SESSION['anno_nascita']); ?></p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Cognome:</strong> <?php echo htmlspecialchars($_SESSION['cognome']); ?></p>
                                            <p><strong>Nickname:</strong> <?php echo htmlspecialchars($_SESSION['nickname']); ?></p>
                                            <p><strong>Luogo di nascita:</strong> <?php echo htmlspecialchars($_SESSION['luogo_nascita']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiche utente -->
                        <div class="col-12 col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Le tue statistiche</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Skill dell'utente -->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary me-2"><?php echo count($skills); ?></span>
                                                <strong><a href="curriculum.php">Competenze</a></strong>
                                            </div>
                                            <?php if (!empty($skills) && count($skills) > 0): ?>
                                                <div class="small text-muted">
                                                    <?php
                                                    $skillNames = array_column($skills, 'competenza');
                                                    echo implode(', ', array_slice($skillNames, 0, 3));
                                                    if (count($skillNames) > 3) echo "...";
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="small text-muted">Nessuna competenza inserita</div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Finanziamenti dell'utente -->
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-success me-2"><?php echo count($finanziamenti); ?></span>
                                                <strong><a href="../public/finanziamenti.php" class="text-success">Finanziamenti</a></strong>
                                            </div>
                                            <?php if (!empty($finanziamenti)): ?>
                                                <p class="small text-muted">Totale: <?php echo number_format($totale_finanziamenti, 2); ?>€</p>
                                            <?php else: ?>
                                                <p class="small text-muted">Nessun finanziamento effettuato</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Informazioni creatore (se applicable) -->
                                    <?php if ($_SESSION['is_creatore']): ?>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($_SESSION['nr_progetti']); ?></span>
                                                    <strong>Progetti creati</strong>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="progress w-50 position-relative" style="height: 20px;">
                                                        <div class="progress-bar fw-bold bg-success"
                                                             style="width: <?php echo htmlspecialchars($_SESSION['affidabilita']); ?>%; height: 100%;">
                                                        </div>
                                                        <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black">
                                                            <?php echo htmlspecialchars($_SESSION['affidabilita']); ?>%
                                                        </div>
                                                    </div>
                                                    <strong class="mx-2">Affidabilità</strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom row -->
    <div class="row g-4 mt-4">
        <!-- Card Progetti (solo per creatori) -->
        <?php if ($_SESSION['is_creatore']): ?>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">I tuoi progetti</h4>
                        <a href="../public/progetto_crea.php" class="btn btn-sm btn-light">Crea Nuovo</a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($progettiError)): ?>
                            <p class="text-danger"><?php echo htmlspecialchars($progettiError); ?></p>
                        <?php else: ?>
                            <?php if (!empty($progetti)): ?>
                                <div class="list-group">
                                    <?php foreach ($progetti as $index => $progetto): ?>
                                        <a href="progetto_dettagli.php?nome=<?php echo urlencode($progetto['nome']); ?>"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1"><?php echo htmlspecialchars($progetto['nome']); ?></h5>
                                                <p class="mb-1 small text-muted">Budget: <?php echo number_format($progetto['budget'], 2); ?>€</p>
                                            </div>
                                            <span class="badge <?php echo $progetto['stato'] === 'aperto' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo strtoupper(htmlspecialchars($progetto['stato'])); ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>Non hai creato nessun progetto.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Card Finanziamenti (per tutti gli utenti) -->
        <div class="col-12 <?php echo $_SESSION['is_creatore'] ? 'col-md-6' : 'col-md-12'; ?>">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Finanziamenti recenti</h4>
                    <a href="../public/finanziamenti.php" class="btn btn-sm btn-light">Visualizza tutti</a>
                </div>
                <div class="card-body">
                    <?php if (isset($finError)): ?>
                        <p class="text-danger"><?php echo htmlspecialchars($finError); ?></p>
                    <?php else: ?>
                        <?php if (!empty($finanziamenti)): ?>
                            <div class="list-group">
                                <?php
                                // Mostra solo i primi 5 finanziamenti più recenti
                                $recent_fin = array_slice($finanziamenti, 0, 5);
                                foreach ($recent_fin as $finanziamento):
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-1">
                                                <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($finanziamento['nome_progetto']); ?>">
                                                    <?php echo htmlspecialchars($finanziamento['nome_progetto']); ?>
                                                </a>
                                            </h5>
                                            <span class="badge bg-success">
                                                <?php echo htmlspecialchars(number_format($finanziamento['importo'], 2)); ?>€
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 small text-muted">
                                                Reward: <?php echo htmlspecialchars($finanziamento['codice_reward']); ?>
                                            </p>
                                            <small>
                                                <?php echo htmlspecialchars(date('d/m/Y', strtotime($finanziamento['data']))); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($finanziamenti) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="../public/finanziamenti.php" class="btn btn-outline-warning">
                                        Visualizza tutti i <?php echo count($finanziamenti); ?> finanziamenti
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>Nessun finanziamento registrato.</p>
                            <a href="../public/progetti.php" class="btn btn-primary">Esplora progetti da finanziare</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>