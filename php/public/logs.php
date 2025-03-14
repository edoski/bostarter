<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
checkAuth();
checkAdmin();

// === DATABASE ===
$logs = [];
try {
    $collections = $GLOBALS['mongodb']->listCollections();
    $collectionArray = [];

    foreach ($collections as $collection) {
        $collectionArray[] = $collection->getName();
    }

    $selectedCollection = $_GET['collection'] ?? '';

    if (!empty($selectedCollection) && in_array($selectedCollection, $collectionArray)) {
        $cursor = $GLOBALS['mongodb']->$selectedCollection->find(
            [],
            ['sort' => ['timestamp' => -1], 'limit' => 50]
        );

        foreach ($cursor as $document) {
            $timestamp = isset($document['timestamp']) ?
                $document['timestamp']->toDateTime()->format('Y-m-d H:i:s') : 'N/D';

            $logs[] = [
                'collection' => $selectedCollection,
                'timestamp' => $timestamp,
                'action' => $document['action'] ?? 'unknown',
                'user' => $document['user_email'] ?? 'unknown',
                'source' => $document['source_file'] ?? 'N/D',
                'from' => $document['from_page'] ?? 'N/D',
                'to' => $document['to_page'] ?? 'N/D',
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

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <!-- Collection selector -->
        <div class="card mb-4">
            <div class="card-header">Seleziona Collezione</div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($collectionArray as $coll): ?>
                        <div class="col-md-3 mb-2">
                            <a href="?collection=<?php echo urlencode($coll); ?>"
                               class="btn btn-<?php echo ($selectedCollection == $coll) ? 'primary' : 'outline-primary'; ?> w-100">
                                <?php echo htmlspecialchars($coll); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($selectedCollection)): ?>
            <?php if (empty($logs)): ?>
                <div class="alert alert-info">Nessun log trovato per questa collezione</div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Timestamp</th>
                        <th>Azione</th>
                        <th>Utente</th>
                        <th>Messaggio</th>
                        <th>Dettagli</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['user']); ?></td>
                            <td><?php echo htmlspecialchars($log['message']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#logModal<?php echo md5($log['timestamp'].$log['action']); ?>">
                                    Dettagli
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modals for log details -->
                <?php foreach ($logs as $log): ?>
                    <div class="modal fade" id="logModal<?php echo md5($log['timestamp'].$log['action']); ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Dettagli Log</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Collezione:</strong> <?php echo htmlspecialchars($log['collection']); ?></p>
                                            <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($log['timestamp']); ?></p>
                                            <p><strong>Azione:</strong> <?php echo htmlspecialchars($log['action']); ?></p>
                                            <p><strong>Utente:</strong> <?php echo htmlspecialchars($log['user']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Da:</strong> <?php echo htmlspecialchars($log['from']); ?></p>
                                            <p><strong>A:</strong> <?php echo htmlspecialchars($log['to']); ?></p>
                                            <p><strong>Fonte:</strong> <?php echo htmlspecialchars($log['source']); ?></p>
                                            <p><strong>Messaggio:</strong> <?php echo htmlspecialchars($log['message']); ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6>Dati:</h6>
                                    <pre class="bg-light p-2"><?php echo htmlspecialchars($log['data']); ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">Seleziona una collezione per visualizzare i log</div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require '../components/footer.php'; ?>