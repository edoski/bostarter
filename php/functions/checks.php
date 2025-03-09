<?php
require_once __DIR__ . '/../functions/checks.php';

// === GENERIC CHECKS ===
// La sezione seguente contenente funzioni che effettuano controlli di sicurezza primitivi e comuni a più pagine
// Si definisce primitivo un controllo che fa un singolo tipo di verifica generico, come controllare se un utente è loggato

/**
 * Funzione per controllare se l'utente è loggato. Se non lo è, reindirizza alla pagina di login.
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
 * Funzione per controllare se l'utente è un amministratore. Se non lo è, reindirizza alla pagina home.
 */
function checkAdmin(): void
{
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        redirect(
            false,
            "Devi essere un amministratore per accedere a questa pagina.",
            "../public/home.php"
        );
    }
}

/**
 * Funzione per controllare se l'utente è il creatore di un progetto
 *
 * @param string $email L'email dell'utente da controllare.
 * @param string $nomeProgetto Il nome del progetto da controllare.
 *
 * @return bool Restituisce true se l'utente è il creatore del progetto, false altrimenti.
 */
function checkProgettoOwner(string $email, string $nomeProgetto): bool
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
 * Funzione per controllare se l'utente ha selezionato un progetto valido
 */
function checkProjectSelected(): void
{
    if (!isset($_POST['nome_progetto'])) {
        redirect(
            false,
            "Errore selezionamento progetto. Riprova.",
            "../public/progetti.php"
        );
    }
}

// === COMMENTO CHECKS ===
// La sezione seguente contiene funzioni che effettuano controlli di sicurezza specifici per i commenti di un progetto

/**
 * Funzione per controllare se l'utente ha inserito un commento valido (lungo almeno 3 caratteri)
 */
function check_Commento_validInsert(): void {
    if (!isset($_POST['commento']) || strlen(trim($_POST['commento'])) < 3) {
        redirect(
            false,
            "Il commento deve essere lungo almeno 3 caratteri.",
            "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
        );
    }
}

/**
 * Funzione per controllare se è stato selezionato un commento valido da eliminare
 */
function check_Commento_validIdSelected(): void {
    if (!isset($_POST['id_commento'])) {
        redirect(
            false,
            "Errore eliminazione commento. Riprova.",
            "../public/progetto_dettagli.php?nome=" . urlencode($_POST['nome_progetto'])
        );
    }
}

// === COMMENTO RISPOSTA CHECKS ===
// La sezione seguente contiene funzioni che effettuano controlli di sicurezza specifici per le risposte ai commenti di un progetto

/*
 * Funzione per controllare se sono stati inviati tutti i dati necessari per la risposta di un commento
 */
function check_CommentoRisposta_validComment(): void
{
    if (!isset($_POST['id_commento']) || !isset($_POST['nome_progetto'])) {
        redirect(
            false,
            "Errore durante il controllo del commento. Riprova.",
            "../public/progetti.php"
        );
    }
}