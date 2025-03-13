<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// === DATABASE ===
// Recupero le candidature inviate dall'utente
try {
    $in = ['p_email' => $_SESSION['email']];
    $candidature_utente = sp_invoke('sp_partecipante_selectAllByUtente', $in);
} catch (PDOException $ex) {
    $candidature_utente = [];
    $candidatureError = "Errore nel recupero delle candidature: " . $ex->errorInfo[2];
}

// Se l'utente è un creatore, recupero anche le candidature ricevute dai suoi progetti
$candidature_ricevute = [];
if ($_SESSION['is_creatore']) {
    try {
        $in = ['p_email_creatore' => $_SESSION['email']];
        $candidature_ricevute = sp_invoke('sp_partecipante_selectAllByCreatore', $in);
    } catch (PDOException $ex) {
        $candidatureRicevuteError = "Errore nel recupero delle candidature ricevute: " . $ex->errorInfo[2];
    }
}
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- Messaggio di successo/errore post-azione -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <h1 class="mb-4">Candidature</h1>

    <!-- Sezione per utenti creatori - Candidature ricevute -->
    <?php if ($_SESSION['is_creatore']): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Candidature Ricevute</h4>
            </div>
            <div class="card-body">
                <?php if (isset($candidatureRicevuteError)): ?>
                    <p class="text-danger"><?php echo htmlspecialchars($candidatureRicevuteError); ?></p>
                <?php elseif (empty($candidature_ricevute)): ?>
                    <p>Non hai ricevuto candidature per i tuoi progetti.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Progetto</th>
                                <th>Profilo</th>
                                <th>Candidato</th>
                                <th>Stato</th>
                                <th>Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($candidature_ricevute as $candidatura): ?>
                                <tr>
                                    <td>
                                        <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($candidatura['nome_progetto']); ?>">
                                            <?php echo htmlspecialchars($candidatura['nome_progetto']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($candidatura['nome_profilo']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($candidatura['candidato_nickname']); ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($candidatura['email_utente']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($candidatura['stato'] == 'potenziale'): ?>
                                            <span class="badge bg-warning">In attesa</span>
                                        <?php elseif ($candidatura['stato'] == 'accettato'): ?>
                                            <span class="badge bg-success">Accettato</span>
                                        <?php elseif ($candidatura['stato'] == 'rifiutato'): ?>
                                            <span class="badge bg-danger">Rifiutato</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($candidatura['stato'] == 'potenziale'): ?>
                                            <form action="../actions/candidatura_update.php" method="post" class="d-inline">
                                                <input type="hidden" name="email_candidato" value="<?php echo htmlspecialchars($candidatura['email_utente']); ?>">
                                                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($candidatura['nome_progetto']); ?>">
                                                <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($candidatura['nome_profilo']); ?>">
                                                <input type="hidden" name="nuovo_stato" value="accettato">
                                                <button type="submit" class="btn btn-sm btn-success">Accetta</button>
                                            </form>
                                            <form action="../actions/candidatura_update.php" method="post" class="d-inline ms-1">
                                                <input type="hidden" name="email_candidato" value="<?php echo htmlspecialchars($candidatura['email_utente']); ?>">
                                                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($candidatura['nome_progetto']); ?>">
                                                <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($candidatura['nome_profilo']); ?>">
                                                <input type="hidden" name="nuovo_stato" value="rifiutato">
                                                <button type="submit" class="btn btn-sm btn-danger">Rifiuta</button>
                                            </form>
                                        <?php else: ?>
                                            -
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

    <!-- Sezione per tutti gli utenti - Candidature inviate -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Candidature Inviate</h4>
        </div>
        <div class="card-body">
            <?php if (isset($candidatureError)): ?>
                <p class="text-danger"><?php echo htmlspecialchars($candidatureError); ?></p>
            <?php elseif (empty($candidature_utente)): ?>
                <p>Non hai ancora inviato candidature.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Progetto</th>
                            <th>Creatore</th>
                            <th>Profilo</th>
                            <th>Stato</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($candidature_utente as $candidatura): ?>
                            <tr>
                                <td>
                                    <a href="../public/progetto_dettagli.php?nome=<?php echo urlencode($candidatura['nome_progetto']); ?>">
                                        <?php echo htmlspecialchars($candidatura['nome_progetto']); ?>
                                    </a>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($candidatura['descrizione']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($candidatura['creatore_nickname']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['nome_profilo']); ?></td>
                                <td>
                                    <?php if ($candidatura['stato'] == 'potenziale'): ?>
                                        <span class="badge bg-warning">In attesa</span>
                                    <?php elseif ($candidatura['stato'] == 'accettato'): ?>
                                        <span class="badge bg-success">Accettato</span>
                                    <?php elseif ($candidatura['stato'] == 'rifiutato'): ?>
                                        <span class="badge bg-danger">Rifiutato</span>
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