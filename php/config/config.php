<?php
// === SETUP ===
// Classi per MongoDB
require_once __DIR__ . '/../vendor/autoload.php';

// === VARIABLES ===
const DB_HOST = "db";
const DB_NAME = "BOSTARTER";
const DB_USER = "root";
const DB_PASS = "password";
const MONGO_URI = "mongodb://mongodb:27017";
const MONGO_DB = "BOSTARTER_LOG";

// MySQL Connection
try {
    $GLOBALS['pdo'] = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
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
foreach (glob(__DIR__ . '/../functions/*.php') as $filename) require_once $filename;