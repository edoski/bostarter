<?php
// Classi per MongoDB
require_once __DIR__ . '/../vendor/autoload.php';

// MySQL Configuration
const DB_HOST = '127.0.0.1';
const DB_NAME = 'BOSTARTER';
const DB_USER = 'root';
const DB_PASS = '339273';

// MongoDB Configuration
const MONGO_URI = 'mongodb://localhost:27017';
const MONGO_DB = 'BOSTARTER_LOG';

try {
    // MySQL Connection
    $GLOBALS['pdo'] = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // MongoDB Connection
    try {
//        $mongoClient = new MongoDB\Client(MONGO_URI);
//        $GLOBALS['mongodb'] = $mongoClient->selectDatabase(MONGO_DB);
    } catch (Exception $ex) {
        error_log("Errore di connessione MongoDB: " . $ex->getMessage());
        die("Errore di connessione MongoDB: " . $ex->getMessage());
    }

    // Funzioni per invocare le stored procedure, controlli di sicurezza, logging, ecc.
    require_once __DIR__ . '/../functions/sp_invoke.php';
    require_once __DIR__ . '/../functions/checks.php';
    require_once __DIR__ . '/../functions/redirect.php';
    require_once __DIR__ . '/../functions/log.php';

} catch (PDOException $ex) {
    die("Errore di connessione MySQL: " . $ex->getMessage());
}