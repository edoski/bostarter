<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Modifica Competenza: <?= htmlspecialchars($competenza_selezionata); ?></h3>
        </div>
        <div class="card-body">
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Modifica Livello Competenza</h5>
                </div>
                <div class="card-body">
                    <form action="<?=generate_url('skill_profilo_update') ?>" method="post">
                        <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($_GET['nome']); ?>">
                        <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($profilo_selezionato); ?>">
                        <input type="hidden" name="competenza" value="<?= htmlspecialchars($competenza_selezionata); ?>">

                        <div class="mb-3">
                            <label class="form-label">Competenza</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($competenza_selezionata); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="nuovo_livello" class="form-label">Nuovo Livello Richiesto (0-5)</label>
                            <input type="number" name="nuovo_livello" class="form-control"
                                   value="<?= htmlspecialchars($livello_selezionato); ?>"
                                   required min="0" max="5">
                            <div class="form-text text-danger">
                                Attenzione: Candidature esistenti con competenze inferiori a questo livello verranno rimosse.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $_GET['nome'], 'profilo' => $profilo_selezionato]) ?>"
                               class="btn btn-secondary">Annulla</a>
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Sei sicuro di voler aggiornare il livello della competenza? Candidature esistenti potrebbero essere rimosse.')">
                                Aggiorna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>