<?php
/**
 * Registra un evento compiuto da azioni (/actions) nel log di MongoDB "BOSTARTER_LOG".
 *
 * @param bool $success Esito dell'operazione
 * @param string $collection Nome della collezione (corrispondente alla tabella MySQL)
 * @param string $action Azione eseguita (insert, update, delete)
 * @param string $procedure Nome della stored procedure eseguita
 * @param string $email Email dell'utente che ha eseguito l'operazione
 * @param array $data Dati dell'operazione
 * @param string $message Messaggio visualizzato all'utente
 */
function log_event(bool $success, string $collection, string $action, string $procedure, string $email, array $data, string $message): void
{
    global $mongodb;

    // Se MongoDB non è disponibile, non blocco l'esecuzione della pagina
    if (!$mongodb) return;

    // Momento e sorgente dell'evento
    $timestamp = new MongoDB\BSON\UTCDateTime() ?? new DateTime() ?? "N/A";
    $trace = debug_backtrace();
    // TODO see if this correctly logs for both /public and /actions
    $source = basename($trace[2]['file'] ?? $trace[1]['file'] ?? "N/A");

    try {
        $log = [
            'success' => $success,
            'table' => $collection,
            'action' => $action,
            'procedure' => $procedure,
            'timestamp' => $timestamp,
            'email' => $email,
            'data' => $data,
            'source' => $source,
            'message' => $message
        ];

        // Inserimento del log nella collezione specifica
        $mongodb->selectCollection($collection)->insertOne($log);
    } catch (Exception $ex) {
        error_log("Errore nel log MongoDB: " . $ex->getMessage());
    }
}