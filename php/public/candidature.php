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

/**
 * Renderizza la tabella delle candidature.
 *
 * @param array $candidature Array di candidature
 * @param bool $is_ricevute Se sono candidature ricevute o inviate
 * @return string HTML della tabella
 */
function render_candidature_table(array $candidature, bool $is_ricevute): string
{
    ob_start();
    ?>
    <?php if ($candidature['failed']): ?>
    <p class="text-danger">C'è stato un errore nel recupero delle candidature<?= $is_ricevute ? ' ricevute' : ' inviate'; ?>.</p>
    <?php elseif (empty($candidature['data'])): ?>
        <p>Non hai <?= $is_ricevute ? 'ricevuto' : 'ancora inviato'; ?> candidature.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <?php if ($is_ricevute): ?>
                        <th>Progetto</th>
                        <th>Profilo</th>
                        <th>Candidato</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    <?php else: ?>
                        <th>Progetto</th>
                        <th>Creatore</th>
                        <th>Profilo</th>
                        <th>Stato</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($candidature['data'] as $candidatura): ?>
                    <tr>
                        <?php if ($is_ricevute): ?>
                            <td>
                                <a href="<?=generate_url('progetto_dettagli', ['nome' => $candidatura['nome_progetto']]); ?>">
                                    <?= htmlspecialchars($candidatura['nome_progetto']); ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($candidatura['nome_profilo']); ?></td>
                            <td>
                                <?= htmlspecialchars($candidatura['candidato_nickname']); ?>
                                <small class="text-muted d-block"><?= htmlspecialchars($candidatura['email_utente']); ?></small>
                            </td>
                            <td><?= render_badge($candidatura['stato']); ?></td>
                            <td>
                                <?php if ($candidatura['stato'] == 'potenziale'): ?>
                                    <form action="<?=generate_url('candidatura_update') ?>" method="post" class="d-inline">
                                        <input type="hidden" name="email_candidato" value="<?= htmlspecialchars($candidatura['email_utente']); ?>">
                                        <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($candidatura['nome_progetto']); ?>">
                                        <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($candidatura['nome_profilo']); ?>">
                                        <input type="hidden" name="nuovo_stato" value="accettato">
                                        <button type="submit" class="btn btn-sm btn-success">Accetta</button>
                                    </form>
                                    <form action="<?=generate_url('candidatura_update') ?>" method="post" class="d-inline ms-1">
                                        <input type="hidden" name="email_candidato" value="<?= htmlspecialchars($candidatura['email_utente']); ?>">
                                        <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($candidatura['nome_progetto']); ?>">
                                        <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($candidatura['nome_profilo']); ?>">
                                        <input type="hidden" name="nuovo_stato" value="rifiutato">
                                        <button type="submit" class="btn btn-sm btn-danger">Rifiuta</button>
                                    </form>
                                <?php else: ?>
                                    <p class="fw-bold">-</p>
                                <?php endif; ?>
                            </td>
                        <?php else: ?>
                            <td>
                                <a href="<?=generate_url('progetto_dettagli', ['nome' => $candidatura['nome_progetto']]); ?>">
                                    <?= htmlspecialchars($candidatura['nome_progetto']); ?>
                                </a>
                                <small class="text-muted d-block"><?= htmlspecialchars($candidatura['descrizione']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($candidatura['creatore_nickname']); ?></td>
                            <td><?= htmlspecialchars($candidatura['nome_profilo']); ?></td>
                            <td><?= render_badge($candidatura['stato']); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php return ob_get_clean();
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
                <?php echo render_candidature_table($candidature_ricevute, true); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- CANDIDATURE INVIATE (ALL) -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Candidature Inviate</h4>
        </div>
        <div class="card-body">
            <?php echo render_candidature_table($candidature_inviate, false); ?>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>