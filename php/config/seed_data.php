<?php
// === CONFIG ===
// Esegui questo script UNA VOLTA per popolare il DB con valori di default non inseribili mediante bostarter_demo.sql
require_once __DIR__ . '/config.php';

// === ACTION ===
/**
 * Inserisce una singola foto nel DB per un dato progetto.
 */
function seedProgettoFoto(string $nomeProgetto, string $emailCreatore, string $imagePath): void
{
    if (!file_exists($imagePath)) {
        echo "ERROR: File not found: $imagePath\n";
        return;
    }

    // Leggo il contenuto binario dell'immagine
    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        echo "ERROR: Failed to read: $imagePath\n";
        return;
    }

    $in = [
        'p_nome_progetto' => $nomeProgetto,
        'p_email_creatore' => $emailCreatore,
        'p_foto' => $imageData
    ];

    try {
        sp_invoke('sp_foto_insert', $in);
    } catch (PDOException $ex) {
        echo "ERROR: " . $ex->getMessage() . "\n";
        print_r($ex->errorInfo);
        die();
    }
}

/**
 * Inserisce una singola foto nel DB per una reward di un progetto.
 */
function seedProgettoReward(string $codiceReward, string $nomeProgetto, string $emailCreatore, string $descrizioneReward, string $imagePath, float $minImporto): void
{
    if (!file_exists($imagePath)) {
        echo "File not found: $imagePath\n";
        return;
    }

    // Leggo il contenuto binario dell'immagine
    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        echo "Failed to read: $imagePath\n";
        return;
    }

    $in = [
        'p_codice' => $codiceReward,
        'p_nome_progetto' => $nomeProgetto,
        'p_email_creatore' => $emailCreatore,
        'p_descrizione' => $descrizioneReward,
        'p_foto' => $imageData,
        'p_min_importo' => $minImporto
    ];

    try {
        sp_invoke('sp_reward_insert', $in);
    } catch (PDOException $ex) {
        print $ex->getMessage();
        die();
    }
}

/**
 * Inserisce un singolo finanziamento nel DB per un dato progetto.
 */
function seedProgettoFinanziamento(string $emailUtente, string $nomeProgetto, string $codiceReward, float $importo): void
{
    $in = [
        'p_email' => $emailUtente,
        'p_nome_progetto' => $nomeProgetto,
        'p_codice_reward' => $codiceReward,
        'p_importo' => $importo
    ];

    try {
        sp_invoke('sp_finanziamento_insert', $in);
    } catch (PDOException $ex) {
        print $ex->getMessage();
        die();
    }
}

// ========== SEED DATA ==========

$path = __DIR__ . '/../img/';

// === ProgettoAlpha (SOFTWARE) ===
// Inserisco le foto del progetto
seedProgettoFoto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/ProgettoAlpha_Cover.jpg'
);

seedProgettoFoto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW1.jpg'
);

seedProgettoFoto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW2.jpg'
);

seedProgettoFoto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW3.jpg'
);

// Inserisco le rewards del progetto
seedProgettoReward(
    'RWD1_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Do deserunt ullamco aliquip ad consequat Lorem minim irure.',
    $path . '/RWD1.jpg',
    150.00
);

seedProgettoReward(
    'RWD2_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Occaecat do elit consequat esse voluptate cillum fugiat.',
    $path . '/RWD2.jpg',
    300.00
);

seedProgettoReward(
    'RWD3_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Aliquip ea duis excepteur dolor elit proident ipsum qui.',
    $path . '/RWD3.jpg',
    500.00
);

seedProgettoReward(
    'RWD_Default',
    'ProgettoAlpha',
    'bob@example.com',
    'Commodo ipsum dolor dolore ullamco aliqua dolor aliqua.',
    $path . '/RWD_Default.jpg',
    0.01
);

// Inserisco i finanziamenti per il progetto
seedProgettoFinanziamento(
    'charlie@example.com',
    'ProgettoAlpha',
    'RWD1_Alpha',
    1150.00
);

seedProgettoFinanziamento(
    'diana@example.com',
    'ProgettoAlpha',
    'RWD2_Alpha',
    1211.00
);

seedProgettoFinanziamento(
    'oscar@example.com',
    'ProgettoAlpha',
    'RWD3_Alpha',
    1523.00
);

// === ProgettoBeta (HARDWARE) ===
// Inserisco le foto del progetto
seedProgettoFoto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/ProgettoBeta_Cover.jpg'
);

seedProgettoFoto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW1.jpg'
);

seedProgettoFoto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW2.jpg'
);

seedProgettoFoto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW3.jpg'
);

// Inserisco le rewards del progetto
seedProgettoReward(
    'RWD1_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Quis Lorem sit tempor ex eiusmod aliqua sint aliqua incididunt laborum occaecat.',
    $path . '/RWD1.jpg',
    150.00
);

seedProgettoReward(
    'RWD2_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Aute mollit laboris veniam laborum id sunt magna consequat enim adipisicing.',
    $path . '/RWD2.jpg',
    300.00
);

seedProgettoReward(
    'RWD3_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Magna tempor in amet sit incididunt magna elit nostrud laborum in.',
    $path . '/RWD3.jpg',
    500.00
);

seedProgettoReward(
    'RWD_Default',
    'ProgettoBeta',
    'diana@example.com',
    'Commodo ipsum dolor dolore ullamco aliqua dolor aliqua sint laborum in.',
    $path . '/RWD_Default.jpg',
    0.01
);

// Inserisco i finanziamenti per il progetto
seedProgettoFinanziamento(
    'edoardo.galli3@studio.unibo.it',
    'ProgettoBeta',
    'RWD1_Beta',
    2050.00
);

seedProgettoFinanziamento(
    'karen@example.com',
    'ProgettoBeta',
    'RWD2_Beta',
    1900.00
);

seedProgettoFinanziamento(
    'bob@example.com',
    'ProgettoBeta',
    'RWD3_Beta',
    1600.00
);

// === FINANZIAMENTI/REWARD PER ALTRI PROGETTI ===

// ProgettoGamma
seedProgettoReward(
    'RWD_Default',
    'ProgettoGamma',
    'grace@example.com',
    'Aliqua laboris laborum laborum laborum.',
    $path . '/RWD_Default.jpg',
    0.01
);
seedProgettoFinanziamento(
    'frank@example.com',
    'ProgettoGamma',
    'RWD_Default',
    3200.00
);

// ProgettoDelta
seedProgettoReward(
    'RWD_Default',
    'ProgettoDelta',
    'ivan@example.com',
    'Aliqua laboris laborum laborum laborum.',
    $path . '/RWD_Default.jpg',
    0.01
);
seedProgettoFinanziamento(
    'heidi@example.com',
    'ProgettoDelta',
    'RWD_Default',
    3075.00
);

// ProgettoKappa
seedProgettoReward(
    'RWD_Default',
    'ProgettoKappa',
    'ivan@example.com',
    'Aliqua laboris laborum laborum laborum.',
    $path . '/RWD_Default.jpg',
    0.01
);

// ProgettoEpsilon
seedProgettoReward(
    'RWD_Default',
    'ProgettoEpsilon',
    'karen@example.com',
    'Aliqua laboris laborum laborum laborum.',
    $path . '/RWD_Default.jpg',
    0.01
);
seedProgettoFinanziamento(
    'oscar@example.com',
    'ProgettoEpsilon',
    'RWD_Default',
    12000.00
);