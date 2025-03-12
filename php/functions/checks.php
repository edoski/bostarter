<?php
require_once __DIR__ . '/../functions/checks.php';

// === GENERIC CHECKS ===
// La sezione seguente contenente funzioni che effettuano controlli di sicurezza generici e comuni a più pagine

/**
 * Controlla se l'utente si è autenticato. Se non lo è, reindirizza alla pagina di login.
 */
function checkAuth(): void
{
    if (!isset($_SESSION['email'])) {
        redirect(
            false,
            "Devi effettuare il login per accedere a questa pagina.",
            "../public/login.php"
        );
    }
}

/**
 * Controlla se l'utente è un amministratore. Se non lo è, reindirizza alla pagina home.
 */
function checkAdmin(): void
{
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        redirect(
            false,
            "Non sei autorizzato ad effettuare questa operazione.",
            "../public/home.php"
        );
    }
}

/**
 * Controlla se l'utente è un creatore. Se non lo è, reindirizza alla pagina home.
 */
function checkCreatore(): void
{
    if (!isset($_SESSION['is_creatore']) || !$_SESSION['is_creatore']) {
        redirect(
            false,
            "Non sei autorizzato ad effettuare questa operazione.",
            "../public/home.php"
        );
    }
}

/**
 * Controlla se l'utente è il creatore del progetto. Se non lo è, reindirizza alla pagina del progetto.
 *
 * @param string $nomeProgetto Il nome del progetto da controllare.
 *
 * @throws PDOException Se non è il creatore del progetto.
 */
function checkProgettoOwner(string $nomeProgetto): void
{
    if (!($_SESSION['is_creatore'] && isProgettoOwner($_SESSION['email'], $nomeProgetto))) {
        redirect(
            false,
            "Non sei autorizzato ad effettuare questa operazione.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nomeProgetto)
        );
    }
}

/**
 * Funzione helper per checkProgettoOwner. Controlla se l'utente è il creatore di un progetto.
 *
 * @param string $email L'email dell'utente da controllare.
 * @param string $nomeProgetto Il nome del progetto da controllare.
 *
 * @return bool Restituisce true se l'utente è il creatore del progetto, false altrimenti.
 */
function isProgettoOwner(string $email, string $nomeProgetto): bool
{
    try {
        $in = [
            'p_email' => $email,
            'p_nome_progetto' => $nomeProgetto
        ];

        // sp_util_progetto_owner_exists restituisce true se l'utente è il creatore del progetto, false altrimenti
        return sp_invoke('sp_util_progetto_owner_exists', $in)[0]['is_owner'];
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il controllo del creatore del progetto: " . $ex->errorInfo[2],
            "../public/progetti.php"
        );

        return false;
    }
}

/**
 * Controlla se il progetto è aperto. Se non lo è, lancia un errore e reindirizza alla pagina del progetto.
 *
 * @param string $nomeProgetto Il nome del progetto da controllare.
 *
 * @throws PDOException Se il progetto è chiuso.
 */
function checkProgettoAperto(string $nomeProgetto): void
{
    try {
        $in = ['p_nome_progetto' => $nomeProgetto];
        sp_invoke('sp_util_progetto_is_aperto', $in);
    } catch (PDOException) {
        redirect(
            false,
            "Il progetto è chiuso.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nomeProgetto)
        );
    }
}

/**
 * Verifica che tutte le variabili specificate siano impostate in $_POST.
 * Se una variabile non è impostata, reindirizza (default a progetti.php) con un messaggio di errore.
 *
 * @param array $vars Array di nomi delle variabili da controllare
 * @param string $location Percorso di reindirizzamento in caso di errore
 */
function checkSetVars(array $vars, string $location = "../public/progetti.php"): void
{
    foreach ($vars as $var) {
        if (!isset($_POST[$var])) {
            redirect(
                false,
                "Errore nel passaggio di variabili POST (Parametro mancante: '$var')",
                $location
            );
        }
    }
}