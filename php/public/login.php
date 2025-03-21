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
            <h1 class="text-center">Login</h1>

            <form action="../actions/login_handler.php" method="POST">
                <!-- EMAIL -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Inserisci il tuo indirizzo email"
                            required
                    >
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
                            required
                    >
                </div>

                <!-- CODICE DI SICUREZZA -->
                <div class="mb-3">
                    <label for="codice_sicurezza" class="form-label">Codice di Sicurezza (ADMIN ONLY)</label>
                    <input
                            type="password"
                            class="form-control"
                            id="codice_sicurezza"
                            name="codice_sicurezza"
                            placeholder="Inserisci il codice di sicurezza"
                    >
                </div>

                <!-- SUBMIT -->
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <!-- REGISTER -->
            <p class="text-center mt-3">
                Non hai un account? <a href="<?= htmlspecialchars(generate_url('register')) ?>"> Registrati</a>
            </p>

            <!-- AUTOLOGIN, UNCOMMENT IN SEDE D'ESAME -->
            <div class="card w-100 mt-5">
                <div class="card-header bg-secondary text-white pb-0 pt-3">
                    <p class="fw-bold text-center">AUTOLOGIN (UTILE IN SEDE D'ESAME)</p>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center gap-3">
                    <!-- Admin Alice -->
                    <form action="../actions/login_handler.php" method="POST" class="d-inline">
                        <input type="hidden" name="email" value="alice@example.com">
                        <input type="hidden" name="password" value="passAlice">
                        <input type="hidden" name="codice_sicurezza" value="admincode123">
                        <button type="submit" class="btn btn-danger me-2">Alice (Admin)</button>
                    </form>

                    <!-- Creatore Bob (Software) -->
                    <form action="../actions/login_handler.php" method="POST" class="d-inline">
                        <input type="hidden" name="email" value="bob@example.com">
                        <input type="hidden" name="password" value="passBob">
                        <button type="submit" class="btn btn-primary">Bob (Creatore)</button>
                    </form>

                    <!-- Creatore Diana (Hardware) -->
                    <form action="../actions/login_handler.php" method="POST" class="d-inline">
                        <input type="hidden" name="email" value="diana@example.com">
                        <input type="hidden" name="password" value="passDiana">
                        <button type="submit" class="btn btn-primary">Diana (Creatore)</button>
                    </form>

                    <!-- Regolare Edo -->
                    <form action="../actions/login_handler.php" method="POST" class="d-inline">
                        <input type="hidden" name="email" value="edoardo.galli3@studio.unibo.it">
                        <input type="hidden" name="password" value="passEdo">
                        <button type="submit" class="btn btn-success">Edo (Regolare)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require '../components/footer.php'; ?>