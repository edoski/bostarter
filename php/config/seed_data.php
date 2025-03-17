<?php
// === SETUP ===
// Questo script viene eseguito automaticamente UNA VOLTA per popolare il DB con valori di default non inseribili mediante bostarter_demo.sql
require_once __DIR__ . '/config.php';
$path = __DIR__ . '/../img/';

echo "=== SEEDING seed_data.php START! ===\n";

// === ProgettoAlpha (SOFTWARE) ===

echo "Seeding ProgettoAlpha... ";

// Inserisco le foto per ProgettoAlpha
seed_progetto_foto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/ProgettoAlpha_Cover.jpg'
);

seed_progetto_foto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW1.jpg'
);

seed_progetto_foto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW2.jpg'
);

seed_progetto_foto(
    'ProgettoAlpha',
    'bob@example.com',
    $path . '/SW3.jpg'
);

// Inserisco le rewards per ProgettoAlpha
seed_progetto_default_reward('ProgettoAlpha', 'bob@example.com');

seed_progetto_reward(
    'RWD1_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Do deserunt ullamco aliquip ad consequat Lorem minim irure.',
    $path . '/RWD1.jpg',
    150.00
);

seed_progetto_reward(
    'RWD2_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Occaecat do elit consequat esse voluptate cillum fugiat.',
    $path . '/RWD2.jpg',
    300.00
);

seed_progetto_reward(
    'RWD3_Alpha',
    'ProgettoAlpha',
    'bob@example.com',
    'Aliquip ea duis excepteur dolor elit proident ipsum qui.',
    $path . '/RWD3.jpg',
    500.00
);

// Inserisco i finanziamenti per ProgettoAlpha
seed_progetto_finanziamento(
    'charlie@example.com',
    'ProgettoAlpha',
    'RWD1_Alpha',
    1150.00
);

seed_progetto_finanziamento(
    'diana@example.com',
    'ProgettoAlpha',
    'RWD2_Alpha',
    1211.00
);

seed_progetto_finanziamento(
    'oscar@example.com',
    'ProgettoAlpha',
    'RWD3_Alpha',
    1523.00
);

echo "OK.\n";

// === ProgettoBeta (HARDWARE) ===

echo "Seeding ProgettoBeta... ";

// Inserisco le foto per ProgettoBeta
seed_progetto_foto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/ProgettoBeta_Cover.jpg'
);

seed_progetto_foto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW1.jpg'
);

seed_progetto_foto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW2.jpg'
);

seed_progetto_foto(
    'ProgettoBeta',
    'diana@example.com',
    $path . '/HW3.jpg'
);

// Inserisco le rewards per ProgettoBeta
seed_progetto_default_reward('ProgettoBeta', 'diana@example.com');

seed_progetto_reward(
    'RWD1_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Quis Lorem sit tempor ex eiusmod aliqua sint aliqua incididunt laborum occaecat.',
    $path . '/RWD1.jpg',
    150.00
);

seed_progetto_reward(
    'RWD2_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Aute mollit laboris veniam laborum id sunt magna consequat enim adipisicing.',
    $path . '/RWD2.jpg',
    300.00
);

seed_progetto_reward(
    'RWD3_Beta',
    'ProgettoBeta',
    'diana@example.com',
    'Magna tempor in amet sit incididunt magna elit nostrud laborum in.',
    $path . '/RWD3.jpg',
    500.00
);

// Inserisco i finanziamenti per ProgettoBeta
seed_progetto_finanziamento(
    'edoardo.galli3@studio.unibo.it',
    'ProgettoBeta',
    'RWD1_Beta',
    2050.00
);

seed_progetto_finanziamento(
    'karen@example.com',
    'ProgettoBeta',
    'RWD2_Beta',
    1900.00
);

seed_progetto_finanziamento(
    'bob@example.com',
    'ProgettoBeta',
    'RWD3_Beta',
    1600.00
);

echo "OK.\n";

// === FINANZIAMENTI PER ALTRI PROGETTI ===

echo "Seeding remaining projects... ";

// ProgettoGamma
seed_progetto_default_reward('ProgettoGamma', 'grace@example.com');
seed_progetto_finanziamento(
    'frank@example.com',
    'ProgettoGamma',
    'RWD_Default',
    3200.00
);

// ProgettoDelta
seed_progetto_default_reward('ProgettoDelta', 'ivan@example.com');
seed_progetto_finanziamento(
    'heidi@example.com',
    'ProgettoDelta',
    'RWD_Default',
    3075.00
);

// ProgettoEpsilon
seed_progetto_default_reward('ProgettoEpsilon', 'karen@example.com');
seed_progetto_finanziamento(
    'oscar@example.com',
    'ProgettoEpsilon',
    'RWD_Default',
    12000.00
);

echo "OK.\n";

echo "=== SEEDING seed_data.php COMPLETE! ===\n";