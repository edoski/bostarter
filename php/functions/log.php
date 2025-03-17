<?php
/**
 * Registra un evento nel log di MongoDB "BOSTARTER_LOG".
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
    // Se MongoDB non è disponibile, non blocco l'esecuzione della pagina
    if (!isset($GLOBALS['mongodb'])) {
        return;
    }

    try {
        // Dati del log
        $log_data = [
            'success' => $success,
            'table' => $collection,
            'action' => $action,
            'procedure' => $procedure,
            'timestamp' => new MongoDB\BSON\UTCDateTime() ?? new DateTime() ?? "N/A",
            'email' => $email,
            'data' => $data,
            'source' => basename(debug_backtrace()[2]['file']) ?? "N/A",
            'message' => $message
        ];

        // Inserimento del log nella collezione specifica
        $GLOBALS['mongodb']->selectCollection($collection)->insertOne($log_data);
    } catch (Exception $ex) {
        error_log("Errore nel log MongoDB: " . $ex->getMessage());
    }
}