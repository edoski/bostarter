<?php
/**
 * Registra un evento nel log di MongoDB.
 *
 * @param string $collection Nome della collezione (corrispondente alla tabella MySQL)
 * @param string $action Azione eseguita (insert, update, delete)
 * @param array $data Dati dell'operazione
 * @param string|null $user_email Email dell'utente che ha eseguito l'operazione
 * @param string|null $from_page Pagina di origine
 * @param string|null $to_page Pagina di destinazione
 * @param string|null $message Messaggio visualizzato all'utente
 */
function log_event(string $collection, string $action, array $data, ?string $user_email = null,
                  ?string $from_page = null, ?string $to_page = null,
                  ?string $message = null): void
{
    // Se MongoDB non è disponibile, non bloccare l'esecuzione
    if (!isset($GLOBALS['mongodb'])) {
        return;
    }

    // Se l'utente è loggato, recuperiamo la sua email
    if (!$user_email && isset($_SESSION['email'])) {
        $user_email = $_SESSION['email'];
    }

    // Ottieni informazioni sul chiamante
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $source_file = $trace[0]['file'] ?? 'unknown';

    try {
        // Dati del log
        $log_data = [
            'collection' => $collection,
            'action' => $action,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'user_email' => $user_email,
            'data' => $data,
            'source_file' => $source_file
        ];

        if ($from_page) $log_data['from_page'] = $from_page;
        if ($to_page) $log_data['to_page'] = $to_page;
        if ($message) $log_data['message'] = $message;

        // Inserimento del log nella collezione specifica
        $GLOBALS['mongodb']->selectCollection($collection)->insertOne($log_data);
    } catch (Exception $ex) {
        error_log("Errore nel log MongoDB: " . $ex->getMessage());
    }
}