<?php
// === CONFIG ===
session_start();
require_once '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. L'utente è il creatore del progetto
checkProgettoOwner($_GET['nome']);

// 3. Controllo che l'attributo sia stato specificato
if (!isset($_GET['attr']) || !isset($_GET['nome'])) {
    redirect(
        false,
        "Parametro mancante",
        "../public/progetti.php"
    );
}

// 4. Controllo che il progetto sia aperto
checkProgettoAperto($_GET['nome']);

// === DATABASE ===
// Recupero il progetto e il suo tipo
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $progetto = sp_invoke('sp_progetto_select', $in)[0];
    $progetto['tipo'] = sp_invoke('sp_util_progetto_type', $in)[0]['tipo_progetto'];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del progetto: " . $ex->errorInfo[2],
        "../public/progetti.php"
    );
}
?>

<?php require '../components/header.php'; ?>
<div class="container my-4">
    <!-- Messaggio di successo/errore post-azione -->
    <?php include '../components/error_alert.php'; ?>
    <?php include '../components/success_alert.php'; ?>

    <!-- Tasto per tornare indietro -->
    <div class="d-flex justify-content-end">
        <button class="btn btn-warning mb-3">
            <a href="../public/progetto_dettagli.php?nome=<?php echo $_GET['nome']; ?>"
               class="text-black text-decoration-none">Torna al Progetto</a>
        </button>
    </div>

    <!-- Rendering del componente corretto in base all'attributo specificato -->
    <?php switch ($_GET['attr']) {
        case "descrizione":
            // Update descrizione e insert/delete foto del progetto
            require '../components/progetto_aggiorna_descrizione.php';
            break;
        case "budget":
            // Update budget del progetto
            require '../components/progetto_aggiorna_budget.php';
            break;
        case "reward":
            // Update/insert di reward del progetto
            require '../components/progetto_aggiorna_reward.php';
            break;
        case "profilo":
            // Update/insert/delete di profili del progetto (software)
            require '../components/progetto_aggiorna_profili.php';
            break;
        case "componenti":
            // Update/insert/delete di componenti del progetto (hardware)
            require '../components/progetto_aggiorna_componenti.php';
            break;
        default:
            redirect(
                false,
                "Attributo non valido.",
                "../public/progetto_dettagli.php?nome=" . $_GET['nome']
            );
            break;
    } ?>
</div>
<?php require '../components/footer.php'; ?>