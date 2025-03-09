<?php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'BOSTARTER';
const DB_USER = 'root';
const DB_PASS = '339273';

try {
    $GLOBALS['pdo'] = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Include le funzioni per invocare le stored procedure, controlli di sicurezza, ecc.
    require_once __DIR__ . '/../functions/sp_invoke.php';
    require_once __DIR__ . '/../functions/checks.php';
    require_once __DIR__ . '/../functions/redirect.php';
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}