<?php
// === SETUP ===
require_once __DIR__ . '/../config/config.php';

// === GENERIC CHECKS ===
// La sezione seguente contenente funzioni che effettuano controlli di sicurezza generici e comuni a più pagine

/**
 * Controlla se l'utente si è autenticato. Se non lo è, reindirizza alla pagina di login.
 */
function check_auth(): void
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
function check_admin(): void
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
 * Controlla se l'utente è il creatore di un progetto.
 *
 * @param string $email L'email dell'utente da controllare.
 * @param string $nome_progetto Il nome del progetto da controllare.
 *
 * @return bool Restituisce true se l'utente è il creatore del progetto, false altrimenti.
 */
function is_progetto_owner(string $email, string $nome_progetto): bool
{
    try {
        $in = [
            'p_email' => $email,
            'p_nome_progetto' => $nome_progetto
        ];

        // sp_util_progetto_owner_exists restituisce true se l'utente è il creatore del progetto, false altrimenti
        return $_SESSION['is_creatore'] && sp_invoke('sp_util_progetto_owner_exists', $in)[0]['is_owner'];
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
 * @param string $nome_progetto Il nome del progetto da controllare.
 *
 * @throws PDOException Se il progetto è chiuso.
 */
function check_progetto_aperto(string $nome_progetto): void
{
    try {
        $in = ['p_nome_progetto' => $nome_progetto];
        sp_invoke('sp_util_progetto_is_aperto', $in);
    } catch (PDOException) {
        redirect(
            false,
            "Il progetto è chiuso.",
            "../public/progetto_dettagli.php?nome=" . urlencode($nome_progetto)
        );
    }
}

/**
 * Verifica che tutte le variabili specificate siano impostate in $_POST.
 * Se una variabile non è impostata, reindirizza (default a home.php) con un messaggio di errore.
 *
 * @param array $post Array di nomi delle variabili da controllare in $_POST.
 */
function check_POST(array $post): void
{
    foreach ($post as $var) {
        if (!isset($_POST[$var])) {
            redirect(
                false,
                "Errore nel passaggio di variabili POST (Parametro mancante: '$var')",
                "../public/home.php"
            );
        }
    }
}