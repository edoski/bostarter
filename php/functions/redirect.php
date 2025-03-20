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
    $outcome = $success ? 'success' : 'error';
    $_SESSION[$outcome] = $message;
    header("Location: $location");
    exit;
}

/**
 * Registra l'evento come operazione di successo e reindirizza l'utente.
 *
 * @param string $collection Il nome della collezione interessata.
 * @param string $action L'azione eseguita.
 * @param string $procedure La stored procedure eseguita.
 * @param string $email L'email dell'utente coinvolto.
 * @param array $data I dati associati all'evento da loggare.
 * @param string $location La pagina a cui reindirizzare l'utente.
 * @param string $message Il messaggio di successo da mostrare all'utente.
 */
function success(string $collection, string $action, string $procedure, string $email, array $data, string $location, string $message): void
{
    // === LOGGING ===
    log_event(
        true,
        $collection,
        $action,
        $procedure,
        $email,
        $data,
        $message
    );

    // === REDIRECT ===
    redirect(true, $message, $location);
}

/**
 * Registra l'evento come operazione fallita e reindirizza l'utente.
 *
 * @param string $collection Il nome della collezione interessata.
 * @param string $action L'azione eseguita.
 * @param string $procedure La stored procedure eseguita.
 * @param string $email L'email dell'utente coinvolto.
 * @param array $data I dati associati all'evento da loggare.
 * @param string $location La pagina a cui reindirizzare l'utente.
 * @param string $message Il messaggio di errore da mostrare all'utente.
 */
function fail(string $collection, string $action, string $procedure, string $email, array $data, string $location, string $message): void
{
    // === LOGGING ===
    log_event(
        false,
        $collection,
        $action,
        $procedure,
        $email,
        $data,
        $message
    );

    // === REDIRECT ===
    redirect(false, $message, $location);
}