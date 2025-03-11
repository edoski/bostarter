<div>
    <h5 class="mb-3">Competenze Attuali</h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Competenza</th>
                <th>Livello</th>
                <th>Azioni</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($competenzeSelezionate)): ?>
                <?php foreach ($competenzeSelezionate as $competenza): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($competenza['competenza']); ?></td>
                        <td><?php echo htmlspecialchars($competenza['livello']); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <!-- Link for editing -->
                                <a href="?attr=profilo&nome=<?php echo urlencode($_GET['nome']); ?>&profilo=<?php echo urlencode($profiloSelezionato); ?>&competenza=<?php echo urlencode($competenza['competenza']); ?>&livello=<?php echo urlencode($competenza['livello']); ?>"
                                   class="btn btn-sm btn-warning">
                                    Modifica
                                </a>
                                <form action="../actions/skill_profilo_delete.php"
                                      method="post" class="d-inline">
                                    <input type="hidden" name="nome_progetto"
                                           value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                                    <input type="hidden" name="nome_profilo"
                                           value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                                    <input type="hidden" name="competenza"
                                           value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-danger ms-1"
                                            onclick="return confirm('Sei sicuro di voler eliminare questa competenza dal profilo?')">
                                        Elimina
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Nessuna competenza
                        associata a questo profilo
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- Form per aggiungere nuove competenze al profilo -->
    <h5 class="mb-3">Aggiungi Nuova Competenza</h5>
    <form action="../actions/skill_profilo_insert.php" method="post">
        <input type="hidden" name="nome_progetto"
               value="<?php echo htmlspecialchars($_GET['nome']); ?>">
        <input type="hidden" name="nome_profilo"
               value="<?php echo htmlspecialchars($profiloSelezionato); ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="competenza" class="form-label">Competenza</label>
                <select name="competenza" id="competenza" class="form-select"
                        required>
                    <option value="">Seleziona una competenza</option>
                    <?php foreach ($competenzeDisponibili as $competenza): ?>
                        <option value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                            <?php echo htmlspecialchars($competenza['competenza']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="livello" class="form-label">Livello Richiesto
                    (0-5)</label>
                <input type="number" name="livello" id="livello"
                       class="form-control" required min="0" max="5" value="3">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Aggiungi
                </button>
            </div>
        </div>
    </form>

    <div class="mt-3 text-center">
        <a href="?attr=profilo&nome=<?php echo urlencode($_GET['nome']); ?>"
           class="btn btn-secondary">Nuovo Profilo</a>
    </div>
</div>