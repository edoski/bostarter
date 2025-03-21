<?php
// === SETUP ===
session_start();
require_once '../config/config.php';
check_auth();

// === VARIABLES ===
check_GET(['nome', 'attr']);
$email = $_SESSION['email'];
$nome_progetto = $_GET['nome'];
$attr = $_GET['attr'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO_AGGIORNA',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('progetto_dettagli', ['nome' => $nome_progetto])
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
// L'UTENTE È IL CREATORE DEL PROGETTO
$pipeline->check(
    !is_progetto_owner($email, $nome_progetto),
    "Non sei il creatore del progetto."
);

// IL PROGETTO È ANCORA APERTO
$pipeline->invoke('sp_util_progetto_is_aperto', ['p_nome_progetto' => $nome_progetto]);

// === DATA ===
// RECUPERO DATI DEL PROGETTO
$in = ['p_nome_progetto' => $nome_progetto];
$progetto = $pipeline->fetch('sp_progetto_select', $in);
$progetto['tipo'] = $pipeline->fetch('sp_util_progetto_type', $in)['tipo_progetto'];
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- ALERT -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- TORNA INDIETRO -->
    <div class="d-flex justify-content-start">
        <button class="btn btn-warning mb-3">
            <a href="<?= htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $nome_progetto])); ?>"
               class="text-black text-decoration-none">Torna al Progetto</a>
        </button>
    </div>

    <!-- RENDERING DELLA PAGINA IN BASE ALL'ATTRIBUTO -->
    <?php switch ($attr) {
        case "descrizione":
            // UPDATE DESCRIZIONE & INSERT/DELETE FOTO
            require '../components/progetto_aggiorna_descrizione.php';
            break;
        case "budget":
            // UPDATE BUDGET
            require '../components/progetto_aggiorna_budget.php';
            break;
        case "rewards":
            // UPDATE/INSERT REWARD
            require '../components/progetto_aggiorna_reward.php';
            break;
        case "profili":
            // UPDATE/INSERT/DELETE PROFILI (SOFTWARE)
            require '../components/progetto_aggiorna_profili.php';
            break;
        case "componenti":
            // UPDATE/INSERT/DELETE COMPONENTI (HARDWARE)
            require '../components/progetto_aggiorna_componenti.php';
            break;
        default:
            redirect(
                false,
                "Attributo non valido.",
                        htmlspecialchars(generate_url('progetto_dettagli', ['nome' => $nome_progetto]))
            );
            break;
    } ?>
</div>
<?php require '../components/footer.php'; ?>