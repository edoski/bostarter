<?php
// === SETUP ===
// Classi per MongoDB
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Funzione per ottenere una variabile d'ambiente
 *
 * @param string $key Nome della variabile d'ambiente
 * @return string Valore della variabile d'ambiente
 *
 * @throws Exception
 */
function require_env(string $key): string
{
    $value = getenv($key);
    if ($value === false) {
        throw new Exception("ENV. $key NON TROVATA");
    }
    return $value;
}

try {
    // MySQL Configuration
    define("DB_HOST", require_env('DB_HOST'));
    define("DB_NAME", require_env('DB_NAME'));
    define("DB_USER", require_env('DB_USER'));
    define("DB_PASS", require_env('DB_PASS'));

    // MongoDB Configuration
    define("MONGO_URI", require_env('MONGO_URI'));
    define("MONGO_DB", require_env('MONGO_DB'));
} catch (Exception $e) {
    error_log("Errore di configurazione: " . $e->getMessage());
}

// MySQL Connection
try {
    $GLOBALS['pdo'] = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    error_log("Errore di connessione MySQL: " . $ex->getMessage());
    die("Errore di connessione MySQL: " . $ex->getMessage());
}

// MongoDB Connection
try {
    $mongoClient = new MongoDB\Client(MONGO_URI);
    $GLOBALS['mongodb'] = $mongoClient->selectDatabase(MONGO_DB);
} catch (Exception $ex) {
    error_log("Errore di connessione MongoDB: " . $ex->getMessage());
}

// === FUNCTIONS ===
// Funzioni per invocare le stored procedure, controlli di sicurezza, logging, ecc.
// Includo tutti i file nella directory "/functions"
foreach (glob(__DIR__ . '/../functions/*.php') as $filename) require_once $filename;