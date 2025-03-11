<?php
// Initialize session variables if not set
if (!isset($_SESSION['temp_profilo'])) {
    $_SESSION['temp_profilo'] = [
        'nome' => '',
        'competenze' => []
    ];
}

// Handle form submission for adding a competency
if (isset($_POST['add_competenza']) && !empty($_POST['competenza'])) {
    $competenza = $_POST['competenza'];
    $livello = intval($_POST['livello']);

    // Add to session if not already exists
    $exists = false;
    foreach ($_SESSION['temp_profilo']['competenze'] as $comp) {
        if ($comp['competenza'] === $competenza) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $_SESSION['temp_profilo']['competenze'][] = [
            'competenza' => $competenza,
            'livello' => $livello
        ];
    }

    // Save profile name
    if (!empty($_POST['nome_profilo'])) {
        $_SESSION['temp_profilo']['nome'] = $_POST['nome_profilo'];
    }
}

// Handle removing a competency
if (isset($_GET['remove_comp'])) {
    $index = intval($_GET['remove_comp']);
    if (isset($_SESSION['temp_profilo']['competenze'][$index])) {
        array_splice($_SESSION['temp_profilo']['competenze'], $index, 1);
    }
}
?>

    <form method="post">
        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">

        <div class="mb-3">
            <label for="nome_profilo" class="form-label fw-bold h4">Profilo</label>
            <input type="text" class="form-control" id="nome_profilo" name="nome_profilo" required
                   value="<?php echo htmlspecialchars($_SESSION['temp_profilo']['nome']); ?>"
                   placeholder="Es. Frontend Developer">
        </div>

        <hr>

        <!-- Lista competenze già aggiunte -->
        <?php if (!empty($_SESSION['temp_profilo']['competenze'])): ?>
            <div class="table-responsive mb-4">
                <h5>Competenze Attuali</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Competenza</th>
                        <th>Livello</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_SESSION['temp_profilo']['competenze'] as $index => $comp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($comp['competenza']); ?></td>
                            <td><?php echo htmlspecialchars($comp['livello']); ?></td>
                            <td class="text-end">
                                <a href="?attr=profilo&nome=<?php echo urlencode($_GET['nome']); ?>&remove_comp=<?php echo $index; ?>"
                                   class="btn btn-sm btn-danger">
                                    Elimina
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr>

        <div class="row my-4">
            <h5>Aggiungi Competenze</h5>
            <div class="col-md-5">
                <label class="form-label">Competenza</label>
                <select name="competenza" class="form-select" required>
                    <option value="">Seleziona una competenza</option>
                    <?php foreach ($competenzeGlobali as $competenza): ?>
                        <option value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                            <?php echo htmlspecialchars($competenza['competenza']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Livello Richiesto (0-5)</label>
                <input type="number" name="livello" class="form-control" required min="0" max="5" value="3">
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-center">
                <button type="submit" name="add_competenza" class="btn btn-success">Aggiungi</button>
            </div>
        </div>
    </form>
    <hr>
<?php if (!empty($_SESSION['temp_profilo']['nome']) && !empty($_SESSION['temp_profilo']['competenze'])): ?>
    <div class="mt-5 text-end">
        <form action="../actions/profilo_insert.php" method="post"
              onsubmit="return confirm('Confermare la creazione del profilo?');">
            <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
            <input type="hidden" name="nome_profilo"
                   value="<?php echo htmlspecialchars($_SESSION['temp_profilo']['nome']); ?>">

            <?php foreach ($_SESSION['temp_profilo']['competenze'] as $index => $comp): ?>
                <input type="hidden" name="competenze[]" value="<?php echo htmlspecialchars($comp['competenza']); ?>">
                <input type="hidden" name="livelli[]" value="<?php echo htmlspecialchars($comp['livello']); ?>">
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Crea Profilo</button>
        </form>
    </div>
<?php endif; ?>