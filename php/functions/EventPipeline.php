<?php
/**
 * Classe per la gestione di eventi e interazioni con il database.
 * - Permette di eseguire una serie di controlli in sequenza e, se uno di essi fallisce, lancia un errore.
 * - Permette di eseguire stored procedure e reindirizzare l'utente in caso di errore o successo.
 * - Permette di mostrare dati all'utente e loggarli, senza reindirizzare l'utente.
 */
class EventPipeline
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
        $collection = $this->context['collection'] ?? "N/A";
        $action = $this->context['action'] ?? "N/A";
        $procedure = $this->context['procedure'] ?? "N/A";
        $email = $_SESSION['email'] ?? $this->context['email'] ?? "N/A";
        $data = $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index') ?? "N/A";
        $message = $message ?? "Errore durante l'operazione.";

        // === ACTION ===
        if ($failure) fail($collection, $action, $procedure, $email, $data, $redirect, $message);
    }

    /**
     * Esegue una stored procedure e restituisce il risultato, selezionando il primo (e unico) record.
     * Se pass_through è false, in caso di errore reindirizza l'utente.
     * Se pass_through è true, in caso di errore ritorna un array con il flag 'failed' senza reindirizzare.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $procedure Nome della stored procedure da invocare
     * @param array|null $params Parametri da passare alla stored procedure
     * @param bool $pass_through Se true, non reindirizza in caso di errore
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     *
     * @return array Il primo (e unico) record oppure array con flag 'failed' e 'data' vuota
     */
    public function fetch(?string $procedure = null, ?array $params = null, bool $pass_through = false, ?string $redirect = null): array
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "N/A";
        $action = $this->context['action'] ?? "N/A";
        $procedure = $procedure ?? $this->context['procedure'] ?? "N/A";
        $email = $_SESSION['email'] ?? $this->context['email'] ?? "N/A";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index') ?? "N/A";

        // === ACTION ===
        try {
            $result = sp_invoke($procedure, $params);
            $data = !empty($result) ? $result[0] : [];
            return $pass_through ? ['data' => $data, 'failed' => false] : $data;
        } catch (PDOException $ex) {
            return $this->handle_fetch_ex($ex, $pass_through, $collection, $action, $procedure, $email, $params, $redirect);
        }
    }

    /**
     * Esegue una stored procedure e restituisce tutti i record.
     * Se pass_through è false, in caso di errore reindirizza l'utente.
     * Se pass_through è true, in caso di errore ritorna un array con il flag 'failed' senza reindirizzare.
     * Se non specificato, usa valori di default (definiti in $context).
     *
     * @param string|null $procedure Nome della stored procedure da invocare
     * @param array|null $params Parametri da passare alla stored procedure
     * @param bool $pass_through Se true, non reindirizza in caso di errore
     * @param string|null $redirect URL di reindirizzamento in caso di errore
     *
     * @return array Tutti i record oppure array con flag 'failed' e 'data' vuota
     */
    public function fetch_all(?string $procedure = null, ?array $params = null, bool $pass_through = true, ?string $redirect = null): array
    {
        // === PARSING ===
        $collection = $this->context['collection'] ?? "N/A";
        $action = $this->context['action'] ?? "VIEW";
        $procedure = $procedure ?? $this->context['procedure'] ?? "N/A";
        $email = $_SESSION['email'] ?? $this->context['email'] ?? "N/A";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index') ?? "N/A";

        // === ACTION ===
        try {
            $data = sp_invoke($procedure, $params);
            return $pass_through ? ['data' => $data, 'failed' => false] : $data;
        } catch (PDOException $ex) {
            return $this->handle_fetch_ex($ex, $pass_through, $collection, $action, $procedure, $email, $params, $redirect);
        }
    }

    /**
     * Esegue una stored procedure senza restituire risultati.
     * Questa funzione è usata per eseguire stored procedure che compiono azioni senza restituire dati.
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
        $collection = $this->context['collection'] ?? "N/A";
        $action = $this->context['action'] ?? "N/A";
        $procedure = $procedure ?? $this->context['procedure'] ?? "N/A";
        $email = $_SESSION['email'] ?? $this->context['email'] ?? "N/A";
        $params = $params ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_fail'] ?? $this->context['redirect'] ?? generate_url('index') ?? "N/A";

        // === ACTION ===
        try {
            sp_invoke($procedure, $params);
        } catch (PDOException $ex) {
            fail($collection, $action, $procedure, $email, $params, $redirect, "Errore durante l'operazione: " . $ex->errorInfo[2]);
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
        $collection = $this->context['collection'] ?? "N/A";
        $action = $this->context['action'] ?? "N/A";
        $procedure = $this->context['procedure'] ?? "N/A";
        $email = $_SESSION['email'] ?? $this->context['email'] ?? "N/A";
        $data = $data ?? $this->context['in'] ?? [];
        $redirect = $redirect ?? $this->context['redirect_success'] ?? $this->context['redirect'] ?? generate_url('index') ?? "N/A";
        $message = $message ?? "Operazione completata con successo.";

        // === REDIRECT ===
        success($collection, $action, $procedure, $email, $data, $redirect, $message);
    }

    /**
     * Gestisce le eccezioni durante le operazioni di database.
     * Registra l'errore e, in base al parametro pass_through, restituisce uno stato di fallimento o reindirizza l'utente.
     *
     * @param PDOException|Exception $ex L'eccezione lanciata durante l'operazione di database
     * @param bool $pass_through Se true, l'errore viene registrato e viene restituito uno stato di fallimento senza reindirizzamento; se false, l'errore viene registrato e l'utente viene reindirizzato
     * @param string $collection Il nome della collezione/tabella associata all'operazione
     * @param string $action L'azione in corso (es. VIEW, UPDATE, INSERT)
     * @param string $procedure La stored procedure che era in esecuzione
     * @param string $email L'email dell'utente che esegue l'azione
     * @param array $params I parametri passati alla stored procedure
     * @param string $redirect L'URL a cui reindirizzare in caso di errore quando pass_through è false
     *
     * @return array Quando pass_through è true, restituisce un array con chiavi 'data', 'failed' e 'error'; quando pass_through è false, restituisce un array vuoto (non utilizzato perché l'utente viene reindirizzato)
     */
    private function handle_fetch_ex(PDOException|Exception $ex, bool $pass_through, string $collection, string $action, string $procedure, string $email, array $params, string $redirect): array
    {
        $error_message = "Errore durante l'operazione: " . $ex->errorInfo[2];

        if ($pass_through) {
            // LOG, NO REDIRECT
            log_event(false, $collection, $action, $procedure, $email, $params, $error_message);
            return ['data' => [], 'failed' => true, 'error' => $error_message];
        } else {
            // LOG & REDIRECT
            fail($collection, $action, $procedure, $email, $params, $redirect, $error_message);
            return [];
        }
    }
}