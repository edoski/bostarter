<?php
/**
 * Reindirizza l'utente ad una pagina, mostrando un messaggio di successo o errore.
 *
 * @param bool $success Se true, mostra un messaggio di successo, altrimenti un messaggio di errore.
 * @param string $message Il messaggio da mostrare all'utente.
 * @param string $location La pagina a cui reindirizzare l'utente.
 */
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