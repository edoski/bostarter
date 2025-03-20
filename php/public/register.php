<?php
// === SETUP ===
session_start();
require '../config/config.php';

// === VALIDATION ===
// L'UTENTE HA GIÀ EFFETTUATO IL LOGIN
if (isset($_SESSION['email'])) redirect(true, "Sei già loggato.", generate_url('home'));
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
<div class="container flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="row justify-content-center w-100 mb-5">
        <div class="col-12 col-md-9 col-lg-6">
            <!-- ALERT -->
            <?php include '../components/error_alert.php'; ?>
            <?php include '../components/success_alert.php'; ?>

            <!-- TITLE -->
            <h1 class="text-center">Registrazione</h1>

            <form action="../actions/register_handler.php" method="POST">
                <!-- EMAIL -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Inserisci la tua email"
                            required>
                </div>

                <!-- NICKNAME -->
                <div class="mb-3">
                    <label for="nickname" class="form-label">Nickname</label>
                    <input
                            type="text"
                            class="form-control"
                            id="nickname"
                            name="nickname"
                            placeholder="Inserisci il tuo nickname"
                            required>
                </div>

                <!-- NOME -->
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input
                            type="text"
                            class="form-control"
                            id="nome"
                            name="nome"
                            placeholder="Inserisci il tuo nome"
                            required>
                </div>

                <!-- COGNOME -->
                <div class="mb-3">
                    <label for="cognome" class="form-label">Cognome</label>
                    <input
                            type="text"
                            class="form-control"
                            id="cognome"
                            name="cognome"
                            placeholder="Inserisci il tuo cognome"
                            required>
                </div>

                <!-- ANNO DI NASCITA -->
                <div class="mb-3">
                    <label for="anno_nascita" class="form-label">Anno di Nascita</label>
                    <input
                            type="number"
                            class="form-control"
                            id="anno_nascita"
                            name="anno_nascita"
                            placeholder="Inserisci il tuo anno di nascita"
                            max="<?php echo date('Y') - 18; ?>"
                            required>
                </div>

                <!-- LUGOO DI NASCITA -->
                <div class="mb-3">
                    <label for="luogo_nascita" class="form-label">Luogo di Nascita</label>
                    <input
                            type="text"
                            class="form-control"
                            id="luogo_nascita"
                            name="luogo_nascita"
                            placeholder="Inserisci il tuo luogo di nascita"
                            required>
                </div>

                <!-- PASSWORD -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Inserisci la tua password"
                            required>
                </div>

                <!-- CONFERMA PASSWORD -->
                <div class="mb-3">
                    <label for="conferma_password" class="form-label">Conferma Password</label>
                    <input
                            type="password"
                            class="form-control"
                            id="conferma_password"
                            name="conferma_password"
                            placeholder="Conferma la tua password"
                            required>
                </div>

                <!-- IS CREATORE -->
                <div class="mb-3 form-check">
                    <input
                            type="checkbox"
                            class="form-check-input"
                            id="is_creatore"
                            name="is_creatore"
                            value="1">
                    <label class="form-check-label fw-bold" for="is_creatore"> Sei un creatore di progetti?</label>
                </div>

                <!-- IS ADMIN -->
                <div class="mb-3 form-check">
                    <input
                            type="checkbox"
                            class="form-check-input"
                            id="is_admin"
                            name="is_admin"
                            value="1">
                    <label class="form-check-label fw-bold" for="is_admin"> Sei un amministratore?</label>
                </div>

                <!-- CODICE DI SICUREZZA -->
                <div class="mb-3">
                    <label for="codice_sicurezza" class="form-label">Codice di Sicurezza (ADMIN ONLY)</label>
                    <input
                            type="password"
                            class="form-control"
                            id="codice_sicurezza"
                            name="codice_sicurezza"
                            minlength="8"
                            placeholder="Inserisci il codice di sicurezza (min. 8 caratteri)"
                            required>
                </div>

                <!-- SUBMIT -->
                <button type="submit" class="btn btn-primary w-100">Registrati</button>
            </form>

            <!-- LOGIN -->
            <p class="text-center mt-3">
                Hai già un account? <a href="<?php echo htmlspecialchars(generate_url('login')); ?>">Accedi</a>
            </p>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>