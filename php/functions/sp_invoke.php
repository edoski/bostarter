<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Interfaccia semplificata per l'invocazione di stored procedure MySQL.
 *
 * @param string $procedure     Il nome della stored procedure da invocare.
 * @param array $in             Array associativo di parametri IN (nome => valore).
 *
 * @return array                Se la stored procedure restituisce un result set, viene restituito un array di record. Altrimenti, un array vuoto.
 * @throws PDOException         Se si verifica un errore durante l'esecuzione della stored procedure.
 */
function sp_invoke(string $procedure, array $in = []): array
{
    // Recupero la connessione al database
    global $pdo;

    // Preparo la lista dei parametri da passare alla stored procedure
    $placeholders = [];
    foreach ($in as $name => $value) {
        $placeholders[] = ':' . $name;
    }

    // Costruisco la stringa da passare al metodo prepare
    $call = "CALL $procedure(" . implode(", ", $placeholders) . ")";

    // Preparo la query
    $stmt = $pdo->prepare($call);

    // Bind dei parametri IN
    foreach ($in as $name => $value) {
        if (is_null($value)) {
            $stmt->bindValue(':' . $name, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':' . $name, $value);
        }
    }

    // Eseguo la stored procedure
    $stmt->execute();

    // Recupero il result set
    $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $result_set;
}