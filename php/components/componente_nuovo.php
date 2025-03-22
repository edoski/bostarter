<!--
/**
 * COMPONENT: componente_nuovo (PARENT: progetto_aggiorna)
 *
 * LEADS: componente_conferma_insert
 *
 * PURPOSE:
 * - Fornisce un form per la creazione di un nuovo componente per un progetto hardware.
 * - Raccoglie nome, descrizione, quantità e prezzo del componente.
 * - Mostra informazioni sul budget attuale e l'impatto potenziale del nuovo componente.
 * - Reindirizza alla pagina di conferma prima dell'inserimento effettivo.
 */
-->

<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Nuovo Componente</h3>
        </div>
        <div class="card-body">
            <?php
            // CALCOLO COSTO TOTALE DEI COMPONENTI
            $costo_totale = 0;
            foreach ($componenti['data'] as $comp) {
                $costo_totale += $comp['prezzo'] * $comp['quantita'];
            }
            ?>

            <!-- ALERT BUDGET -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Informazioni Budget</h5>
                <p>Budget attuale del progetto: <strong><?= number_format($progetto['budget'], 2); ?>€</strong></p>
                <p>Costo totale attuale dei componenti: <strong><?= number_format($costo_totale, 2); ?>€</strong></p>

                <hr>
                <p class="mb-0">
                    <strong>Nota:</strong> Se il costo totale dei componenti supera il budget attuale del progetto,
                    il budget verrà automaticamente incrementato per coprire il costo totale.
                </p>
            </div>

            <!-- CREA NUOVO COMPONENTE -->
            <form action="<?=generate_url('componente_conferma_insert') ?>" method="post">
                <input type="hidden" name="nome_progetto"
                       value="<?= htmlspecialchars($_GET['nome']); ?>">

                <div class="mb-3">
                    <label for="nome_componente" class="form-label fw-bold">Nome Componente</label>
                    <input type="text" class="form-control" id="nome_componente" name="nome_componente" required
                           placeholder="Es. Scheda Arduino">
                </div>

                <div class="mb-3">
                    <label for="descrizione" class="form-label fw-bold">Descrizione</label>
                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required
                              placeholder="Descrizione del componente..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantita" class="form-label fw-bold">Quantità</label>
                        <input type="number" class="form-control" id="quantita" name="quantita" required min="1"
                               placeholder="Es. 5">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prezzo" class="form-label fw-bold">Prezzo Unitario (€)</label>
                        <input type="number" class="form-control" id="prezzo" name="prezzo" required step="0.01" min="0.01"
                               placeholder="Es. 25.99">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Crea Componente</button>
                </div>
            </form>
        </div>
    </div>
</div>