<?php
/**
 * Classe per gestire validazioni in sequenza.
 * - Permette di eseguire una serie di controlli in sequenza e, se uno di essi fallisce, lancia un errore.
 * - Permette di eseguire stored procedure e reindirizzare l'utente in caso di errore o successo.
 * - Logga le operazioni effettuate.
 *
 */
class ValidationPipeline
{
    // Dati di contesto per la validazione, logging, e redirect
    private array $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * Verifica una condizione e, se fallisce, lancia (e logga) l'errore, reindirizzando l'utente ad una pagina specificata.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param bool $failure Condizione da verificare, se true vuol dire che la validazione è fallita
     * @param string|null $message Messaggio di errore da mostrare all'utente
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     */
    public function check(bool $failure, ?string $message, ?string $redirect = null): void
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "UNDEFINED";
        $action = $this->context['action'] ?? "UNDEFINED";
        $procedure = $this->context['procedure'] ?? "UNDEFINED";
        $data = $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index');
        $message = $message ?? "Errore durante l'operazione.";

        // === ACTION ===
        if ($failure) fail($collection, $action, $procedure, $data, $redirect, $message);
    }

    /**
     * Esegue una stored procedure e restituisce il risultato, selezionando il primo (e unico) record.
     * Questa funzione è utile per eseguire stored procedure che restituiscono un solo record.
     * Se la query fallisce, lancia (e logga) l'errore, reindirizzando l'utente ad una pagina specificata.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $procedure Nome della stored procedure da invocare
     * @param array|null $params Parametri da passare alla stored procedure
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     */
    public function fetch(?string $procedure = null, ?array $params = null, ?string $redirect = null): array
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "UNDEFINED";
        $action = $this->context['action'] ?? "UNDEFINED";
        $procedure = $procedure ?? $this->context['procedure'] ?? "UNDEFINED";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index');

        // === ACTION ===
        try {
            return sp_invoke($procedure, $params)[0]; // Il risultato è un array di record, ne prendo il primo (e unico)
        } catch (PDOException $ex) {
            fail(
                $collection, $action, $procedure, $params, $redirect,
                "Errore durante l'operazione: " . $ex->errorInfo[2]
            );
            return [];
        }
    }

    /**
     * Esegue una stored procedure senza restituire risultati.
     * Questa funzione è utile per eseguire stored procedure che compiono azioni senza restituire dati.
     * Se la query fallisce, lancia (e logga) l'errore, reindirizzando l'utente ad una pagina specificata.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $procedure Nome della stored procedure da invocare
     * @param array|null $params Parametri da passare alla stored procedure
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     */
    public function invoke(?string $procedure = null, ?array $params = null, ?string $redirect = null): void
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "UNDEFINED";
        $action = $this->context['action'] ?? "UNDEFINED";
        $procedure = $procedure ?? $this->context['procedure'] ?? "UNDEFINED";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index');

        // === ACTION ===
        try {
            sp_invoke($procedure, $params);
        } catch (PDOException $ex) {
            fail(
                $collection, $action, $procedure, $params, $redirect,
                "Errore durante l'operazione: " . $ex->errorInfo[2]
            );
        }
    }

    /**
     * Esegue una query e restituisce il risultato, selezionando tutti i record.
     * Questa funzione è utile per eseguire stored procedure che restituiscono più record.
     * Se la query fallisce, lancia (e logga) l'errore, reindirizzando l'utente ad una pagina specificata.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $procedure Nome della stored procedure da invocare
     * @param array|null $params Parametri da passare alla stored procedure
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     */
    public function fetch_all(?string $procedure = null, ?array $params = null, ?string $redirect = null): array
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "UNDEFINED";
        $action = $this->context['action'] ?? "UNDEFINED";
        $procedure = $procedure ?? $this->context['procedure'] ?? "UNDEFINED";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index');

        // === ACTION ===
        try {
            return sp_invoke($procedure, $params); // Il risultato è un array di record
        } catch (PDOException $ex) {
            fail(
                $collection, $action, $procedure, $params, $redirect,
                "Errore durante l'operazione: " . $ex->errorInfo[2]
            );
            return [];
        }
    }

    /**
     * Completa con successo l'operazione, effettua il logging e reindirizza l'utente.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $message Messaggio di successo da mostrare all'utente
     * @param array|null $data Dati aggiuntivi da loggare relativi all'operazione
     * @param string|null $redirect URL di reindirizzamento in caso di successo
     */
    public function continue(?string $message = null, ?array $data = null, ?string $redirect = null): void
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "UNDEFINED";
        $action = $this->context['action'] ?? "UNDEFINED";
        $procedure = $this->context['procedure'] ?? "UNDEFINED";
        $data = $data ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_success'] ?? $this->context['redirect'] ?? generate_url('index');

        // === REDIRECT ===
        success(
            $collection, $action, $procedure, $data, $redirect,
            $message ?? "Operazione completata con successo."
        );
    }
}