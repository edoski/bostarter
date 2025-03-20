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
    'collection' => 'CANDIDATURE',
    'action' => 'VIEW',
    'email' => $email,
    'in' => ['p_email' => $email],
];
$pipeline = new EventPipeline($context);

// === DATA ===
// RECUPERO CANDIDATURE INVIATE DALL'UTENTE
$candidature_inviate = $pipeline->fetch_all('sp_partecipante_selectAllByUtente');

// SE UTENTE CREATORE, RECUPERO CANDIDATURE RICEVUTE PER I SUOI PROGETTI
if ($is_creatore) $candidature_ricevute = $pipeline->fetch_all('sp_partecipante_selectAllByCreatore');

// === RENDERING ===
/**
 * Restituisce un badge colorato in base allo stato della candidatura.
 *
 * @param string $stato Lo stato della candidatura (potenziale, accettato, rifiutato)
 * @return string Il badge HTML
 */
function render_badge(string $stato): string
{
    $badges = [
        'potenziale' => '<span class="badge bg-warning">In attesa</span>',
        'accettato' => '<span class="badge bg-success">Accettato</span>',
        'rifiutato' => '<span class="badge bg-danger">Rifiutato</span>'
    ];
    return $badges[$stato] ?? '';
}
?>

<!-- === PAGE ===-->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- ALERTS -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- TITLE -->
    <h1 class="mb-4">Candidature</h1>

    <!-- CANDIDATURE RICEVUTE (CREATORE) -->
    <?php if ($is_creatore): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Candidature Ricevute</h4>
            </div>
            <div class="card-body">
                <?php if ($candidature_ricevute['failed']): ?>
                    <p class="text-danger">C'è stato un errore nel recupero delle candidature ricevute.</p>
                <?php elseif (empty($candidature_ricevute['data'])): ?>
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
                            <?php foreach ($candidature_ricevute['data'] as $candidatura): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $candidatura['nome_progetto']])); ?>">
                                            <?php echo htmlspecialchars($candidatura['nome_progetto']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($candidatura['nome_profilo']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($candidatura['candidato_nickname']); ?>
                                        <small class="text-muted d-block"><?php echo htmlspecialchars($candidatura['email_utente']); ?></small>
                                    </td>
                                    <td><?php echo render_badge($candidatura['stato']); ?></td>
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
                                            <p class="fw-bold">-</p>
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

    <!-- CANDIDATURE INVIATE (ALL) -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Candidature Inviate</h4>
        </div>
        <div class="card-body">
            <?php if ($candidature_inviate['failed']): ?>
                <p class="text-danger">C'è stato un errore nel recupero delle candidature inviate.</p>
            <?php elseif (empty($candidature_inviate['data'])): ?>
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
                        <?php foreach ($candidature_inviate['data'] as $candidatura): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo generate_url('progetto_dettagli', ['nome' => $candidatura['nome_progetto']]); ?>">
                                        <?php echo htmlspecialchars($candidatura['nome_progetto']); ?>
                                    </a>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($candidatura['descrizione']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($candidatura['creatore_nickname']); ?></td>
                                <td><?php echo htmlspecialchars($candidatura['nome_profilo']); ?></td>
                                <td><?php echo render_badge($candidatura['stato']); ?></td>
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