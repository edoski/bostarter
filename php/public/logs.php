<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
global $mongodb;
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];
$collection_selezionata = $_GET['collection'] ?? '';
$only_errors = isset($_GET['errors']) && $_GET['errors'] === '1';

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
/**
 * Recupera i log da una collezione specifica o da tutte le collezioni.
 *
 * @param string $collection_name Nome della collezione (vuoto per tutte le collezioni)
 * @param bool $only_errors Flag per filtrare solo gli errori
 * @param int $limit Numero massimo di log da recuperare
 * @return array Array dei log
 */
function fetch_logs(string $collection_name = '', bool $only_errors = false, int $limit = 100): array
{
    global $mongodb;
    $logs = [];

    // COLLEZIONI DA CUI RECUPERARE I LOG
    $collections_to_query = [];
    if (empty($collection_name)) {
        // RECUPERO TUTTE LE COLLEZIONI
        $collections = $mongodb->listCollections();
        foreach ($collections as $collection) {
            $collections_to_query[] = $collection->getName();
        }
    } else {
        // RECUPERO SOLO DA UNA COLLEZIONE SPECIFICATA
        $collections_to_query = [$collection_name];
    }

    // SE SELEZIONATO, FILTRO PER SOLO ERRORI
    $filter = $only_errors ? ['success' => false] : [];

    // RECUPERO I LOG
    foreach ($collections_to_query as $coll) {
        $cursor = $mongodb->selectCollection($coll)->find(
            $filter,
            ['sort' => ['timestamp' => -1], 'limit' => $limit]
        );

        foreach ($cursor as $document) {
            $timestamp = $document['timestamp']->toDateTime()->format('Y-m-d H:i:s');

            $logs[] = [
                '_id' => (string)$document['_id'],
                'collection' => $coll,
                'timestamp' => $timestamp,
                'success' => $document['success'],
                'action' => $document['action'],
                'procedure' => $document['procedure'],
                'user' => $document['email'],
                'source' => $document['source'],
                'message' => $document['message'],
                'data' => json_encode($document['data'], JSON_PRETTY_PRINT)
            ];
        }
    }

    // ORDINAMENTO PER TIMESTAMP (PIÙ RECENTE PRIMA)
    usort($logs, function ($a, $b) {
        return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
    });

    // LIMITAZIONE DEI RISULTATI (100 DI DEFAULT)
    return array_slice($logs, 0, $limit);
}

try {
    // RECUPERO LISTA DELLE COLLEZIONI
    $collection_list = [];
    $collections = $mongodb->listCollections();
    foreach ($collections as $collection) {
        $collection_list[] = $collection->getName();
    }

    // RECUPERO DEI LOG (FILTRATI PER COLLEZIONE E/O SOLO ERRORI)
    $logs = fetch_logs($collection_selezionata, $only_errors);

} catch (Exception $ex) {
    fail(
        "LOGS",
        "Errore durante il recupero dei log",
        "N/A",
        $email,
        [],
        generate_url('home'),
        $ex->getMessage()
    );
}

// === RENDERING ===
/**
 * Renderizza un modale per il dettaglio di un log.
 *
 * @param array $log Log da visualizzare
 * @return string HTML del modale
 */
function render_log_modal(array $log): string
{
    ob_start();
    ?>
    <div class="modal fade" id="logModal<?= $log['_id']; ?>"
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
    <?php
    return ob_get_clean();
}

/**
 * Renderizza il selettore di collezione.
 *
 * @param array $collection_list Lista delle collezioni disponibili
 * @param string $collection_selezionata Collezione attualmente selezionata
 * @param bool $only_errors Flag per il filtro degli errori
 * @return string HTML del selettore
 */
function render_collection_selector(array $collection_list, string $collection_selezionata, bool $only_errors): string
{
    ob_start();
    ?>
    <div class="card mb-4">
        <div class="card-header">Filtra Log</div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="errorFilter"
                        <?= $only_errors ? 'checked' : ''; ?>
                           onchange="window.location.href='?collection=<?= urlencode($collection_selezionata); ?>&errors=' + (this.checked ? '1' : '0')">
                    <label class="form-check-label" for="errorFilter">Mostra solo errori</label>
                </div>

                <?php if (!empty($collection_selezionata)): ?>
                    <a href="?<?= $only_errors ? 'errors=1' : ''; ?>" class="btn btn-outline-secondary">
                        Mostra tutte le collezioni
                    </a>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="?<?= $only_errors ? 'errors=1' : ''; ?>"
                       class="btn btn-<?= empty($collection_selezionata) ? 'primary' : 'outline-primary'; ?> w-100">
                        Tutte le collezioni
                    </a>
                </div>
                <?php foreach ($collection_list as $coll): ?>
                    <div class="col-md-3 mb-2">
                        <a href="?collection=<?= urlencode($coll); ?><?= $only_errors ? '&errors=1' : ''; ?>"
                           class="btn btn-<?= ($collection_selezionata == $coll) ? 'primary' : 'outline-primary'; ?> w-100">
                            <?= htmlspecialchars($coll); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renderizza la tabella dei log.
 *
 * @param array $logs Array di log
 * @return string HTML della tabella dei log
 */
function render_logs_table(array $logs): string
{
    ob_start();
    ?>
    <?php if (empty($logs)): ?>
    <div class="alert alert-info">Nessun log trovato</div>
<?php else: ?>
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th>Timestamp</th>
            <th>Tabella</th>
            <th>Azione</th>
            <th>Utente</th>
            <th>Stato</th>
            <th>Dettagli</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['timestamp']); ?></td>
                <td><?= htmlspecialchars($log['collection']); ?></td>
                <td><?= htmlspecialchars($log['action']); ?></td>
                <td><?= htmlspecialchars($log['user']); ?></td>
                <td><?= $log['success'] ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Error</span>'; ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#logModal<?= $log['_id']; ?>">
                        Dettagli
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- MODAL PER I LOG -->
    <?php foreach ($logs as $log): ?>
        <?= render_log_modal($log); ?>
    <?php endforeach; ?>
<?php endif; ?>
    <?php
    return ob_get_clean();
}
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <h1 class="mb-4">Logs</h1>

    <!-- FILTRI -->
    <?= render_collection_selector($collection_list, $collection_selezionata, $only_errors); ?>

    <!-- TABELLA LOG -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <?php if (!empty($collection_selezionata)): ?>
                    Log - <?= htmlspecialchars($collection_selezionata); ?>
                <?php else: ?>
                    Tutti i Log
                <?php endif; ?>
                <?php if ($only_errors): ?>
                    <span class="badge bg-danger ms-2">Solo Errori</span>
                <?php endif; ?>
            </h5>
            <span class="badge bg-light text-dark"><?= count($logs); ?> log trovati</span>
        </div>
        <div class="card-body">
            <?= render_logs_table($logs); ?>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>