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
} catch (PDOException $ex) {
    $finanziamenti = [];
    $finError = "Errore nel recupero dei finanziamenti: " . $ex->errorInfo[2];
}
?>

<?php require '../components/header.php'; ?>
    <div class="container flex-grow-1 my-4">
        <!-- Messaggio di successo/errore post-azione -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- Top row -->
        <div class="row g-4">
            <!-- Welcome & Guide Card -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">Welcome!</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text fw-bold"> Ciao <?php echo htmlspecialchars($_SESSION['nickname']); ?>,
                            benvenuto/a su BOSTARTER!</p>
                        <p class="card-text">Ecco una breve guida su come muoverti:</p>
                        <ul>
                            <li><strong>Progetti:</strong> visualizza tutti i progetti o creane uno (se sei un creatore).</li>
                            <li><strong>Statistiche:</strong> visualizza le statistiche pubbliche della piattaforma.</li>
                            <li><strong>Skill:</strong> gestisci le tue competenze e aggiungile al tuo profilo.</li>
                            <li><strong>Logout:</strong> esci dal tuo account in qualsiasi momento.</li>
                        </ul>
                        <p class="card-text"> Usa la barra di navigazione in alto per spostarti tra le varie sezioni del sito.</p>
                    </div>
                </div>
            </div>

            <!-- User Info (Bio) Card -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Il tuo profilo</h4>
                        <div>
                            <?php if (!$_SESSION['is_creatore'] && !$_SESSION['is_admin']): ?>
                                <span class="badge bg-secondary-subtle text-black-50 fs-6">Utente</span>
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
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
                        <p><strong>Cognome:</strong> <?php echo htmlspecialchars($_SESSION['cognome']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <p><strong>Nickname:</strong> <?php echo htmlspecialchars($_SESSION['nickname']); ?></p>
                        <p><strong>Anno di nascita:</strong> <?php echo htmlspecialchars($_SESSION['anno_nascita']); ?></p>
                        <p><strong>Luogo di nascita:</strong> <?php echo htmlspecialchars($_SESSION['luogo_nascita']); ?></p>
                        <?php if ($_SESSION['is_creatore']): ?>
                            <p><strong>Affidabilità:</strong> <?php echo htmlspecialchars($_SESSION['affidabilita']); ?>%</p>
                            <p><strong>Progetti creati:</strong> <?php echo htmlspecialchars($_SESSION['nr_progetti']); ?></p>
                        <?php endif; ?>
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
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">I tuoi progetti</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($progettiError)): ?>
                                <p class="text-danger"><?php echo htmlspecialchars($progettiError); ?></p>
                            <?php else: ?>
                                <?php if (!empty($progetti)): ?>
                                    <?php $rank = 1; ?>
                                    <ul class="list-group">
                                        <?php foreach ($progetti as $progetto): ?>
                                            <li class="list-group-item">
                                                <a href="progetto_dettagli.php?nome=<?php echo urlencode($progetto['nome']); ?>">
                                                    <?php echo $rank++ . '. ' . htmlspecialchars($progetto['nome']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>Non hai creato nessun progetto.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Card Finanziamenti (per tutti gli utenti) -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">Finanziamenti</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($finError)): ?>
                            <p class="text-danger"><?php echo htmlspecialchars($finError); ?></p>
                        <?php else: ?>
                            <?php if (!empty($finanziamenti)): ?>
                                <?php $rank = 1; ?>
                                <ul class="list-group">
                                    <?php foreach ($finanziamenti as $finanziamento): ?>
                                        <li class="list-group-item">
                                            <?php echo $rank++ . '. <strong>Progetto:</strong> <a href="../public/progetto_dettagli.php?nome=' . urlencode($finanziamento['nome_progetto']) . '">' . htmlspecialchars($finanziamento['nome_progetto']) . '</a> - '; ?>
                                            <?php echo '<strong>Importo:</strong> ' . htmlspecialchars(number_format($finanziamento['importo'], 2)) . '€ - '; ?>
                                            <?php echo '<strong>Data:</strong> ' . htmlspecialchars(date('d/m/Y', strtotime($finanziamento['data']))); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Nessun finanziamento registrato.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>