<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <?php echo empty($profiloSelezionato) ? "Nuovo Profilo" : "Modifica: " . htmlspecialchars($profiloSelezionato); ?>
            </h3>
        </div>
        <div class="card-body">
            <!-- Form per modificare una competenza specifica -->
            <?php if (!empty($competenzaSelezionata)): ?>
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Modifica Livello Competenza</h5>
                    </div>
                    <div class="card-body">
                        <form action="../actions/skill_profilo_update.php" method="post">
                            <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                            <input type="hidden" name="competenza" value="<?php echo htmlspecialchars($competenzaSelezionata); ?>">

                            <div class="mb-3">
                                <label class="form-label">Competenza</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($competenzaSelezionata); ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="nuovo_livello" class="form-label">Nuovo Livello Richiesto (0-5)</label>
                                <input type="number" name="nuovo_livello" class="form-control"
                                       value="<?php echo htmlspecialchars($livelloSelezionato); ?>"
                                       required min="0" max="5">
                                <div class="form-text text-danger">
                                    Attenzione: Candidature esistenti con competenze inferiori a questo livello verranno rimosse.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="?attr=profili&nome=<?php echo urlencode($_GET['nome']); ?>&profilo=<?php echo urlencode($profiloSelezionato); ?>"
                                   class="btn btn-secondary">Annulla</a>
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Sei sicuro di voler aggiornare il livello della competenza? Candidature esistenti potrebbero essere rimosse.')">
                                    Aggiorna
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <!-- Form per creare un nuovo profilo -->
            <?php elseif (empty($profiloSelezionato)): ?>
                <form action="../actions/profilo_insert.php" method="post">
                    <input type="hidden" name="nome_progetto"
                           value="<?php echo htmlspecialchars($_GET['nome']); ?>">

                    <div class="mb-3">
                        <label for="nome_profilo" class="form-label fw-bold">Nome Profilo</label>
                        <input type="text" class="form-control" id="nome_profilo" name="nome_profilo" required placeholder="Es. API Developer">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Crea Profilo</button>
                    </div>
                </form>

            <!-- Interfaccia per modificare un profilo esistente -->
            <?php else: ?>
                <!-- Form per aggiornare il nome del profilo-->
                <form action="../actions/profilo_nome_update.php" method="post">
                    <label for="nuovo_nome" class="form-label">Nome Profilo</label>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-group flex-fill me-3">
                            <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                            <input type="hidden" name="profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                            <input type="text" name="nuovo_nome" id="nuovo_nome" class="form-control"
                                   value="<?php echo htmlspecialchars($profiloSelezionato); ?>" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-warning">Aggiorna Nome</button>
                        </div>
                    </div>
                </form>

                <h4 class="mb-3">Competenze</h4>
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
                        <?php if (!empty($competenzeSelezionate) && count($competenzeSelezionate) > 0): ?>
                            <?php foreach ($competenzeSelezionate as $competenza): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($competenza['competenza']); ?></td>
                                    <td><?php echo htmlspecialchars($competenza['livello']); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Link per la modifica -->
                                            <a href="?attr=profili&nome=<?php echo urlencode($_GET['nome']); ?>&profilo=<?php echo urlencode($profiloSelezionato); ?>&competenza=<?php echo urlencode($competenza['competenza']); ?>&livello=<?php echo urlencode($competenza['livello']); ?>"
                                               class="btn btn-sm btn-warning">
                                                Modifica
                                            </a>
                                            <form action="../actions/skill_profilo_delete.php" method="post"
                                                  class="d-inline">
                                                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                                                <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">
                                                <input type="hidden" name="competenza" value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger ms-1"
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
                                <td colspan="3" class="text-center">Nessuna competenza associata a questo profilo</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- Form per aggiungere nuove competenze al profilo -->
                <h5 class="mb-3">Aggiungi una competenza</h5>
                <form action="../actions/skill_profilo_insert.php" method="post">
                    <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                    <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($profiloSelezionato); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="competenza" class="form-label">Competenza</label>
                            <select name="competenza" id="competenza" class="form-select" required>
                                <option value="">Seleziona una competenza</option>
                                <?php foreach ($competenzeDisponibili as $competenza): ?>
                                    <option value="<?php echo htmlspecialchars($competenza['competenza']); ?>">
                                        <?php echo htmlspecialchars($competenza['competenza']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="livello" class="form-label">Livello Richiesto (0-5)</label>
                            <input type="number" name="livello" id="livello" class="form-control" required min="0"
                                   max="5"
                                   value="3">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Aggiungi</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>