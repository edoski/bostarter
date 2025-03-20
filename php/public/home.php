<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
$email = $_SESSION['email'];
$is_creatore = $_SESSION['is_creatore'];
$is_admin = $_SESSION['is_admin'];
$nome = $_SESSION['nome'];
$cognome = $_SESSION['cognome'];
$nickname = $_SESSION['nickname'];
$anno_nascita = $_SESSION['anno_nascita'];
$luogo_nascita = $_SESSION['luogo_nascita'];

// === CONTEXT ===
$context = [
    'collection' => 'HOME',
    'action' => 'VIEW',
    'email' => $email,
    'in' => ['p_email' => $email]
];
$pipeline = new EventPipeline($context);

// === DATA ===
// SE CREATORE, RECUPERO PROGETTI CREATI E RELATIVE STATISTICHE
if ($is_creatore) {
    // PROGETTI CREATI
    $progetti = $pipeline->fetch_all('sp_progetto_selectByCreatore');

    // AFFIDABILITÀ
    $affidabilita = $_SESSION['affidabilita'] = $pipeline->fetch('sp_util_creatore_get_affidabilita')['affidabilita'];

    // NUMERO PROGETTI
    $nr_progetti = $_SESSION['nr_progetti'] = $pipeline->fetch('sp_util_creatore_get_nr_progetti')['nr_progetti'];

    // SOMMA PARTECIPANTI
    $tot_partecipanti = $pipeline->fetch('sp_util_creatore_get_tot_partecipanti')['total_partecipanti'];
}

// RECUPERO FINANZIAMENTI EFFETTUATI DALL'UTENTE
$finanziamenti = $pipeline->fetch_all('sp_finanziamento_selectAllByUtente');

// SOMMA FINANZIAMENTI
$totale_finanziamenti = 0;
foreach ($finanziamenti['data'] as $finanziamento) $totale_finanziamenti += $finanziamento['importo'];

// RECUPERO PROGETTI A CUI L'UTENTE HA PARTECIPATO
$candidature = $pipeline->fetch_all('sp_partecipante_selectAllByUtente');

// RECUPERO SKILL CURRICULUM DELL'UTENTE
$skills = $pipeline->fetch_all('sp_skill_curriculum_selectAll');
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
    <div class="container flex-grow-1 my-4">
        <!-- ALERT -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- TOP ROW -->
        <div class="row g-4">
            <!-- IL TUO PROFILO -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">Il tuo profilo</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- DATI PERSONALI -->
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Dati personali</h5>
                                        <div>
                                            <?php if (!$is_creatore && !$is_admin): ?>
                                                <span class="badge bg-secondary-subtle text-secondary fs-6">Utente</span>
                                            <?php endif; ?>
                                            <?php if ($is_creatore): ?>
                                                <span class="badge bg-primary fs-6">Creatore</span>
                                            <?php endif; ?>
                                            <?php if ($is_admin): ?>
                                                <span class="badge bg-danger fs-6">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                                                <p><strong>Anno di nascita:</strong> <?php echo htmlspecialchars($anno_nascita); ?>
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <p><strong>Cognome:</strong> <?php echo htmlspecialchars($cognome); ?></p>
                                                <p><strong>Nickname:</strong> <?php echo htmlspecialchars($nickname); ?></p>
                                                <p><strong>Luogo di nascita:</strong> <?php echo htmlspecialchars($luogo_nascita); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- LE TUE STATISTICHE -->
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Le tue statistiche</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- SKILL UTENTE -->
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-primary me-2"><?php echo count($skills['data'] ?? 0); ?></span>
                                                    <strong>
                                                        <a href="<?php echo htmlspecialchars(generate_url('curriculum')); ?>" class="text-primary">
                                                            Competenze
                                                        </a>
                                                    </strong>
                                                </div>
                                                <?php if ($skills['failed']): ?>
                                                    <p class="small text-muted">Errore nel recupero delle competenze</p>
                                                <?php elseif (empty($skills['data'])): ?>
                                                    <p class="small text-muted">Nessuna competenza registrata</p>
                                                <?php else: ?>
                                                    <div class="small text-muted">
                                                        <?php
                                                        $skill_list = array_slice(array_column($skills['data'], 'competenza'), 0, 3);
                                                        echo implode(', ', $skill_list) . (count($skills['data']) > 3 ? '...' : '');
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- CANDIDATURE UTENTE -->
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-primary me-2"><?php echo count($candidature['data'] ?? 0); ?></span>
                                                    <strong>
                                                        <a href="<?php echo htmlspecialchars(generate_url('candidature')); ?>" class="text-primary">
                                                            Candidature
                                                        </a>
                                                    </strong>
                                                </div>
                                                <?php if ($candidature['failed']): ?>
                                                    <p class="small text-muted">Errore nel recupero delle candidature</p>
                                                <?php elseif (empty($candidature['data'])): ?>
                                                    <p class="small text-muted">Nessuna candidatura effettuata</p>
                                                <?php else: ?>
                                                    <p class="small text-muted">
                                                        <?php
                                                        $accettate = count(array_filter($candidature['data'], fn($s) => $s['stato'] === 'accettato'));
                                                        echo "Accettate: {$accettate}/" . count($candidature['data']);
                                                        ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>

                                            <!-- FINANZIAMENTI UTENTE -->
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-primary me-2"><?php echo count($finanziamenti); ?></span>
                                                    <strong>
                                                        <a href="<?php echo htmlspecialchars(generate_url('finanziamenti')); ?>" class="text-primary">
                                                            Finanziamenti
                                                        </a>
                                                    </strong>
                                                </div>
                                                <?php if ($finanziamenti['failed']): ?>
                                                    <p class="small text-muted">Errore nel recupero dei
                                                        finanziamenti</p>
                                                <?php elseif (empty($finanziamenti['data'])): ?>
                                                    <p class="small text-muted">Nessun finanziamento effettuato</p>
                                                <?php else: ?>
                                                    <p class="small text-muted">
                                                        Totale: <?php echo number_format($totale_finanziamenti, 2); ?>
                                                        €</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- LE TUE STATISTICHE (CREATORE) -->
                                        <?php if ($is_creatore): ?>
                                            <hr>
                                            <div class="row">
                                                <!-- PROGETTI CREATI -->
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($nr_progetti); ?></span>
                                                        <strong>Progetti</strong>
                                                    </div>
                                                </div>
                                                <!-- PARTECIPANTI -->
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($tot_partecipanti); ?></span>
                                                        <strong>Partecipanti</strong>
                                                    </div>
                                                </div>
                                                <!-- AFFIDABILITÀ -->
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="progress w-50 position-relative"
                                                             style="height: 20px;">
                                                            <div class="progress-bar fw-bold bg-success"
                                                                 style="width: <?php echo htmlspecialchars($_SESSION['affidabilita']); ?>%; height: 100%;">
                                                            </div>
                                                            <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black">
                                                                <?php echo htmlspecialchars($affidabilita); ?>%
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

        <!-- BOTTOM ROW -->
        <div class="row g-4 mt-4">
            <!-- I TUOI PROGETTI (CREATORE) -->
            <?php if ($is_creatore): ?>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">I tuoi progetti</h4>
                            <a href="<?php echo htmlspecialchars(generate_url('progetto_crea')); ?>" class="btn btn-sm btn-light">
                                Crea Nuovo
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($progetti['failed']): ?>
                                <p class="text-danger">Errore nel recupero dei progetti.</p>
                            <?php elseif (empty($progetti['data'])): ?>
                                <p>Non hai creato nessun progetto.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($progetti['data'] as $index => $progetto): ?>
                                        <a href="<?php echo htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $progetto['nome']])); ?>"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1"><?php echo htmlspecialchars($progetto['nome']); ?></h5>
                                                <p class="mb-1 small text-muted">
                                                    Budget: <?php echo number_format($progetto['budget'], 2); ?>€</p>
                                            </div>
                                            <span class="badge <?php echo $progetto['stato'] === 'aperto' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo strtoupper(htmlspecialchars($progetto['stato'])); ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- FINANZIAMENTI RECENTI -->
            <div class="col-12 <?php echo $is_creatore ? 'col-md-6' : 'col-md-12'; ?>">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Finanziamenti recenti</h4>
                        <a href="<?php echo htmlspecialchars(generate_url('finanziamenti')); ?>" class="btn btn-sm btn-light">
                            Visualizza tutti
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($finanziamenti['failed']): ?>
                            <p class="text-danger">Errore nel recupero dei finanziamenti.</p>
                        <?php elseif (empty($finanziamenti['data'])): ?>
                            <p>Nessun finanziamento registrato.</p>
                            <a href="<?php echo htmlspecialchars(generate_url('progetti')); ?>" class="btn btn-primary">
                                Esplora progetti da finanziare
                            </a>
                        <?php else: ?>
                            <div class="list-group">
                                <?php
                                // SOLO 5 FINANZIAMENTI PIÙ RECENTI
                                $recent_fin = array_slice($finanziamenti['data'], 0, 5);
                                foreach ($recent_fin as $finanziamento):
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-1">
                                                <a href="<?php echo htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $finanziamento['nome_progetto']])); ?>">
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
                                                <?php echo htmlspecialchars($finanziamento['data']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($finanziamenti['data']) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="<?php echo htmlspecialchars(generate_url('finanziamenti')); ?>" class="btn btn-outline-warning">
                                        Visualizza tutti i <?php echo count($finanziamenti['data']); ?> finanziamenti
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>