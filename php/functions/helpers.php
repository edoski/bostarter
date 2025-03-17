<?php
/**
 * Inserisce una singola foto nel DB per un dato progetto.
 *
 * @param string $nome_progetto Il nome del progetto a cui appartiene la foto.
 * @param string $email_creatore L'email del creatore del progetto.
 * @param string $image_path Il percorso dell'immagine da inserire.
 */
function seed_progetto_foto(string $nome_progetto, string $email_creatore, string $image_path): void
{
    if (!file_exists($image_path)) {
        error_log("File not found: $image_path");
        return;
    }

    // Leggo il contenuto binario dell'immagine
    $foto = file_get_contents($image_path);
    if ($foto === false) {
        error_log("Failed to read: $image_path");
        return;
    }

    $in = [
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email_creatore,
        'p_foto' => $foto
    ];

    try {
        sp_invoke('sp_foto_insert', $in);
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        die();
    }
}

/**
 * Inserisce una reward per un dato progetto.
 *
 * @param string $codice_reward Il codice della reward.
 * @param string $nome_progetto Il nome del progetto a cui appartiene la reward.
 * @param string $email_creatore L'email del creatore del progetto.
 * @param string $descrizione La descrizione della reward.
 * @param string $image_path Il percorso dell'immagine della reward.
 * @param float $min_importo L'importo minimo per ricevere la reward.
 */
function seed_progetto_reward(string $codice_reward, string $nome_progetto, string $email_creatore, string $descrizione, string $image_path, float $min_importo): void
{
    if (!file_exists($image_path)) {
        echo "File not found: $image_path\n";
        return;
    }

    // Leggo il contenuto binario dell'immagine
    $foto = file_get_contents($image_path);
    if ($foto === false) {
        echo "Failed to read: $image_path\n";
        return;
    }

    $in = [
        'p_codice' => $codice_reward,
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email_creatore,
        'p_descrizione' => $descrizione,
        'p_foto' => $foto,
        'p_min_importo' => $min_importo
    ];

    try {
        sp_invoke('sp_reward_insert', $in);
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        die();
    }
}

/**
 * Inserisce un singolo finanziamento nel DB per un dato progetto.
 *
 * @param string $email L'email dell'utente che ha effettuato il finanziamento.
 * @param string $nome_progetto Il nome del progetto a cui è destinato il finanziamento.
 * @param string $codice_reward Il codice della reward selezionata per il finanziamento.
 * @param float $importo L'importo del finanziamento.
 */
function seed_progetto_finanziamento(string $email, string $nome_progetto, string $codice_reward, float $importo): void
{
    $in = [
        'p_email' => $email,
        'p_nome_progetto' => $nome_progetto,
        'p_codice_reward' => $codice_reward,
        'p_importo' => $importo
    ];

    try {
        sp_invoke('sp_finanziamento_insert', $in);
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        die();
    }
}

/**
 * Inserisce per ogni nuovo progetto la reward di default.
 *
 * @param string $nome_progetto Il nome del progetto appena creato.
 */
function seed_progetto_default_reward(string $nome_progetto, ?string $email_creatore = null): bool {
    $image_path = __DIR__ . '/../img/RWD_Default.jpg';

    if (!file_exists($image_path)) {
        error_log("RWD_Default.jpg not found at $image_path");
        return false;
    }

    $foto = file_get_contents($image_path);
    if ($foto === false) {
        error_log("Failed to read RWD_Default.jpg");
        return false;
    }

    $email_creatore = $email_creatore ?? $_SESSION['email'];

    $in = [
        'p_codice' => 'RWD_Default',
        'p_nome_progetto' => $nome_progetto,
        'p_email_creatore' => $email_creatore,
        'p_descrizione' => 'Reward di default',
        'p_foto' => $foto,
        'p_min_importo' => 0.01
    ];

    try {
        sp_invoke('sp_reward_insert', $in);
        return true;
    } catch (PDOException $ex) {
        error_log("Failed to seed default reward image: " . $ex->getMessage());
        return false;
    }
}