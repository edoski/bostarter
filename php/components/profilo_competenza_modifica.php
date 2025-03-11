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
                    Attenzione: L'aumento del livello richiesto potrebbe comportare il rifiuto
                    automatico di candidature esistenti.
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="?attr=profilo&nome=<?php echo urlencode($_GET['nome']); ?>&profilo=<?php echo urlencode($profiloSelezionato); ?>"
                   class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-warning">Aggiorna</button>
            </div>
        </form>
    </div>
</div>