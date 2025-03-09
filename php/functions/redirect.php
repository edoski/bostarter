<?php
function redirect(bool $success, string $message, string $location): void
{
    if ($success) {
        $_SESSION['success'] = $message;
    } else {
        $_SESSION['error'] = $message;
    }

    header("Location: $location");
    exit;
}