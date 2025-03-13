<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Interfaccia semplificata per l'invocazione di stored procedure MySQL con parametri IN e OUT.
 *
 * @param string $procedureName Il nome della stored procedure da invocare.
 * @param array $inParams       Array associativo di parametri IN (nome => valore).
 * @param array $outParams      Array associativo di parametri OUT (nome => valore), passato per riferimento.
 *
 * @return array                Se la stored procedure restituisce un result set, viene restituito un array di righe. Altrimenti, un array vuoto.
 * @throws PDOException         Se si verifica un errore durante l'esecuzione della stored procedure.
 */
function sp_invoke(string $procedureName, array $inParams = [], array &$outParams = []): array
{
    // Recupero la connessione al database
    global $pdo;

    // Preparo la lista dei parametri da passare alla stored procedure
    $placeholders = [];
    foreach ($inParams as $name => $value) {
        $placeholders[] = ':' . $name;
    }

    // Aggiungo i parametri OUT (se esistono) alla lista
    foreach ($outParams as $outName => $dummy) {
        $placeholders[] = '@' . $outName;
    }

    // Costruisco la stringa da passare al metodo prepare
    $call = "CALL $procedureName(" . implode(", ", $placeholders) . ")";

    // Preparo la query
    $stmt = $pdo->prepare($call);

    // Bind dei parametri IN
    foreach ($inParams as $name => $value) {
        if (is_null($value)) {
            $stmt->bindValue(':' . $name, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':' . $name, $value);
        }
    }

    // Eseguo la stored procedure
    $stmt->execute();

    // Se non sono stati definiti parametri OUT, si assume che la stored procedure restituisca un result set
    if (empty($outParams)) {
        $resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $resultSet;
    }

    // Recupero i parametri OUT (se esistono)
    foreach ($outParams as $outName => &$outValue) {
        $selectStmt = $pdo->query("SELECT @$outName AS value");
        $row = $selectStmt->fetch(PDO::FETCH_ASSOC);
        $outValue = $row['value'] ?? null;
        $selectStmt->closeCursor();
    }

    return [];
}