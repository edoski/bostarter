<?php
/**
 * Registra un evento nel log di MongoDB.
 *
 * @param string $collection Nome della collezione (corrispondente alla tabella MySQL)
 * @param string $action Azione eseguita (insert, update, delete)
 * @param array $data Dati dell'operazione
 * @param string $user_email Email dell'utente che ha eseguito l'operazione
 */
function log_event(string $collection, string $action, array $data, string $user_email): void
{
    // Se MongoDB non è disponibile, non bloccare l'esecuzione
    if (!isset($GLOBALS['mongodb'])) {
        return;
    }

    // Se l'utente è loggato, recuperiamo la sua email
    if (isset($_SESSION['email'])) {
        $user_email = $_SESSION['email'];
    }

    try {
        // Dati del log
        $log_data = [
            'collection' => $collection,
            'action' => $action,
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'user_email' => $user_email,
            'data' => $data
        ];

        // Inserimento del log nella collezione specifica
        $GLOBALS['mongodb']->selectCollection($collection)->insertOne($log_data);
    } catch (Exception $ex) {
        // In caso di errore, registriamo l'errore ma non interrompiamo l'esecuzione
        error_log("Errore nel log MongoDB: " . $ex->getMessage());
        return;
    }
}