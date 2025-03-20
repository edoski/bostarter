<?php
// === CONFIG ===
session_start();
require '../config/config.php';

if (isset($_SESSION['email'])) {
    // OK, reindirizzo alla home
    header("Location: home.php");
} else {
    // NO, reindirizzo al login
    header("Location: login.php");
}
exit;