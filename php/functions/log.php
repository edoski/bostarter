<?php
/**
 * Registra un evento nel log di MongoDB "BOSTARTER_LOG".
 *
 * @param bool $success Esito dell'operazione
 * @param string $collection Nome della collezione (corrispondente alla tabella MySQL)
 * @param string $action Azione eseguita (insert, update, delete)
 * @param string $procedure Nome della stored procedure eseguita
 * @param array $data Dati dell'operazione
 * @param string $message Messaggio visualizzato all'utente
 */
function log_event(bool $success, string $collection, string $action, string $procedure, array $data, string $message): void
{
    // Se MongoDB non è disponibile, non blocco l'esecuzione della pagina
    if (!isset($GLOBALS['mongodb'])) {
        return;
    }

    // Se l'utente è loggato, recupero la sua email
    $email = $_SESSION['email'] ?? "UNAUTHENTICATED";

    try {
        // Dati del log
        $log_data = [
            'success' => $success,
            'table' => $collection,
            'action' => $action,
            'procedure' => $procedure,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'email' => $email,
            'data' => $data,
            'source' => basename(debug_backtrace()[2]['file']),
            'message' => $message
        ];

        // Inserimento del log nella collezione specifica
        $GLOBALS['mongodb']->selectCollection($collection)->insertOne($log_data);
    } catch (Exception $ex) {
        error_log("Errore nel log MongoDB: " . $ex->getMessage());
    }
}