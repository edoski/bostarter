<!--
/**
 * COMPONENT: profilo_nuovo (PARENT: progetto_aggiorna)
 *
 * ACTIONS: profilo_insert
 *
 * PURPOSE:
 * - Fornisce un form per la creazione di un nuovo profilo per un progetto software.
 * - Raccoglie il nome del profilo e lo invia al sistema.
 */
-->

<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Nuovo Profilo</h3>
        </div>
        <div class="card-body">
            <form action="<?=generate_url('profilo_insert') ?>" method="post">
                <input type="hidden" name="nome_progetto"
                       value="<?= htmlspecialchars($_GET['nome']); ?>">

                <div class="mb-3">
                    <label for="nome_profilo" class="form-label fw-bold">Nome Profilo</label>
                    <input type="text" class="form-control" id="nome_profilo" name="nome_profilo" required placeholder="Es. API Developer">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Crea Profilo</button>
                </div>
            </form>
        </div>
    </div>
</div>