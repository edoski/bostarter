<?php
/**
 * PAGE: index
 *
 * LEADS: home, login
 *
 * PURPOSE:
 * - Reindirizza l'utente alla pagina di login se non ha effettuato l'accesso.
 * - Reindirizza l'utente alla home page se ha già effettuato l'accesso.
 * - Funge da punto di ingresso principale per l'applicazione.
 */

// === CONFIG ===
session_start();
require '../config/config.php';

// L'UTENTE È LOGGATO
if (isset($_SESSION['email'])) {
    // OK, REINDIRIZZO A HOME
    header("Location: home.php");
} else {
    // NO, REINDIRIZZO A LOGIN
    header("Location: login.php");
}
exit;