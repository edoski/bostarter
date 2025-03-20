<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
global $mongodb;
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'LOGS',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('home')
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È UN AMMINISTRATORE
$pipeline->check(
    !isset($is_admin) || !$is_admin,
    "Non sei autorizzato a visualizzare questa pagina."
);

// === DATA ===
$logs = [];
$recentLogs = [];

try {
    $collections = $mongodb->listCollections();
    $collection_list = [];

    foreach ($collections as $collection) {
        $collection_list[] = $collection->getName();
    }

    // Get most recent logs across all collections
    foreach ($collection_list as $coll) {
        $cursor = $mongodb->selectCollection($coll)->find(
            [],
            ['sort' => ['timestamp' => -1], 'limit' => 5]
        );

        foreach ($cursor as $document) {
            $timestamp = isset($document['timestamp']) ?
                $document['timestamp']->toDateTime() : new DateTime();

            $recentLogs[] = [
                'collection' => $coll,
                'timestamp' => $timestamp,
                'timestamp_str' => $timestamp->format('Y-m-d H:i:s'),
                'success' => $document['success'] ?? false,
                'action' => $document['action'] ?? 'unknown',
                'procedure' => $document['procedure'] ?? 'unknown',
                'user' => $document['email'] ?? 'unknown',
                'source' => $document['source'] ?? 'N/D',
                'message' => $document['message'] ?? 'N/D',
                'data' => $document['data'] ?? []
            ];
        }
    }

    // Sort recent logs by timestamp
    usort($recentLogs, function ($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });

    // Limit to the most recent logs
    $recentLogs = array_slice($recentLogs, 0, 10);

    // Get collection-specific logs if selected
    $selectedCollection = $_GET['collection'] ?? '';

    if (!empty($selectedCollection) && in_array($selectedCollection, $collection_list)) {
        $cursor = $mongodb->selectCollection($selectedCollection)->find(
            [],
            ['sort' => ['timestamp' => -1], 'limit' => 50]
        );

        foreach ($cursor as $document) {
            $timestamp = isset($document['timestamp']) ?
                $document['timestamp']->toDateTime()->format('Y-m-d H:i:s') : 'N/D';

            $logs[] = [
                'collection' => $selectedCollection,
                'timestamp' => $timestamp,
                'success' => $document['success'] ?? false,
                'action' => $document['action'] ?? 'unknown',
                'procedure' => $document['procedure'] ?? 'unknown',
                'user' => $document['email'] ?? 'unknown',
                'source' => $document['source'] ?? 'N/D',
                'message' => $document['message'] ?? 'N/D',
                'data' => json_encode($document['data'] ?? [], JSON_PRETTY_PRINT)
            ];
        }
    }
} catch (Exception $ex) {
    redirect(
        false,
        'Errore durante il recupero dei log ' . $ex->getMessage(),
        '../public/home.php'
    );
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <h1 class="mb-4">Logs</h1>

        <!-- Recent Logs Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Log Recenti</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentLogs)): ?>
                    <div class="alert alert-info">Nessun log recente trovato</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Tabella</th>
                                <th>Azione</th>
                                <th>Utente</th>
<!--                                <th>Fonte</th>-->
<!--                                <th>Procedura</th>-->
<!--                                <th>Messaggio</th>-->
                                <th>Stato</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['timestamp_str']); ?></td>
                                    <td>
                                        <a href="?collection=<?= urlencode($log['collection']); ?>">
                                            <?= htmlspecialchars($log['collection']); ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($log['action']); ?></td>
                                    <td><?= htmlspecialchars($log['user']); ?></td>
<!--                                    <td>--><?php //echo htmlspecialchars($log['source']); ?><!--</td>-->
<!--                                    <td>--><?php //echo htmlspecialchars($log['procedure']); ?><!--</td>-->
<!--                                    <td>--><?php //echo htmlspecialchars(substr($log['message'], 0, 50)) . (strlen($log['message']) > 50 ? '...' : ''); ?><!--</td>-->
                                    <td>
                                        <?php if ($log['success']): ?>
                                            <span class="badge bg-success">Success</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Error</span>
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

        <!-- Collection selector -->
        <div class="card mb-4">
            <div class="card-header">Seleziona Tabella</div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($collection_list as $coll): ?>
                        <div class="col-md-3 mb-2">
                            <a href="?collection=<?= urlencode($coll); ?>"
                               class="btn btn-<?= ($selectedCollection == $coll) ? 'primary' : 'outline-primary'; ?> w-100">
                                <?= htmlspecialchars($coll); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Collection specific logs -->
        <?php if (!empty($selectedCollection)): ?>
            <?php if (empty($logs)): ?>
                <div class="alert alert-info">Nessun log trovato per questa Tabella</div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Timestamp</th>
                        <th>Azione</th>
                        <th>Utente</th>
                        <th>Fonte</th>
                        <th>Procedura</th>
                        <th>Messaggio</th>
                        <th>Stato</th>
                        <th>Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['timestamp']); ?></td>
                            <td><?= htmlspecialchars($log['action']); ?></td>
                            <td><?= htmlspecialchars($log['user']); ?></td>
                            <td><?= htmlspecialchars($log['source']); ?></td>
                            <td><?= htmlspecialchars($log['procedure']); ?></td>
                            <td><?= htmlspecialchars($log['message']); ?></td>
                            <td><?= $log['success'] ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Error</span>'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#logModal<?= md5($log['timestamp'] . $log['action']); ?>">
                                    Dettagli
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modals for log details -->
                <?php foreach ($logs as $log): ?>
                    <div class="modal fade" id="logModal<?= md5($log['timestamp'] . $log['action']); ?>"
                         tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Dettagli Log</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                        <tr>
                                            <th>Timestamp</th>
                                            <td><?= htmlspecialchars($log['timestamp']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tabella</th>
                                            <td><?= htmlspecialchars($log['collection']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Azione</th>
                                            <td><?= htmlspecialchars($log['action']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Utente</th>
                                            <td><?= htmlspecialchars($log['user']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Fonte</th>
                                            <td><?= htmlspecialchars($log['source']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Procedura</th>
                                            <td><?= htmlspecialchars($log['procedure']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Messaggio</th>
                                            <td><?= htmlspecialchars($log['message']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Stato</th>
                                            <td>
                                                <?php if ($log['success']): ?>
                                                    <span class="badge bg-success">Success</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Error</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <hr>

                                    <h6 class="fw-bold">Data</h6>
                                    <pre class="bg-light p-2 border rounded"><?= htmlspecialchars($log['data']); ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">Seleziona una collezione per visualizzare i log dettagliati</div>
        <?php endif; ?>
    </div>
<?php require '../components/footer.php'; ?>