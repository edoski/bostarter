<!--
/**
 * COMPONENT: profilo_modifica (PARENT: progetto_aggiorna)
 *
 * ACTIONS: profilo_nome_update, skill_profilo_insert, skill_profilo_delete
 *
 * PURPOSE:
 * - Permette la modifica di un profilo esistente di un progetto software.
 * - Consente di aggiornare il nome del profilo.
 * - Visualizza, aggiunge e rimuove competenze associate al profilo.
 * - Gestisce i livelli richiesti per ciascuna competenza.
 */
-->

<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Modifica: <?= htmlspecialchars($profilo_selezionato); ?></h3>
        </div>
        <div class="card-body">
            <form action="<?=generate_url('profilo_nome_update') ?>" method="post">
                <label for="nuovo_nome" class="form-label">Nome Profilo</label>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-group flex-fill me-3">
                        <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($_GET['nome']); ?>">
                        <input type="hidden" name="profilo" value="<?= htmlspecialchars($profilo_selezionato); ?>">
                        <input type="text" name="nuovo_nome" id="nuovo_nome" class="form-control"
                               value="<?= htmlspecialchars($profilo_selezionato); ?>" required>
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
                    <?php if (!empty($competenze_selezionate) && count($competenze_selezionate) > 0): ?>
                        <?php foreach ($competenze_selezionate as $competenza): ?>
                            <tr>
                                <td><?= htmlspecialchars($competenza['competenza']); ?></td>
                                <td><?= htmlspecialchars($competenza['livello']); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $_GET['nome'], 'profilo' => $profilo_selezionato, 'competenza' => $competenza['competenza'], 'livello' => $competenza['livello']]); ?>"
                                           class="btn btn-sm btn-warning">
                                            Modifica
                                        </a>
                                        <form action="<?=generate_url('skill_profilo_delete') ?>" method="post"
                                              class="d-inline">
                                            <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($_GET['nome']); ?>">
                                            <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($profilo_selezionato); ?>">
                                            <input type="hidden" name="competenza" value="<?= htmlspecialchars($competenza['competenza']); ?>">
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

            <!-- AGGIUNGI COMPETENZA AL PROFILO -->
            <h5 class="mb-3">Aggiungi una competenza</h5>
            <form action="<?=generate_url('skill_profilo_insert') ?>" method="post">
                <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($_GET['nome']); ?>">
                <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($profilo_selezionato); ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="competenza" class="form-label">Competenza</label>
                        <select name="competenza" id="competenza" class="form-select" required>
                            <option value="">Seleziona una competenza</option>
                            <?php foreach ($competenze_disponibili as $competenza): ?>
                                <option value="<?= htmlspecialchars($competenza['competenza']); ?>">
                                    <?= htmlspecialchars($competenza['competenza']); ?>
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
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Sei sicuro di voler aggiungere questa competenza al profilo? Candidature esistenti potrebbero essere rimosse.')">
                            Aggiungi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>