<!--
/**
 * COMPONENT: componente_modifica (PARENT: progetto_aggiorna)
 *
 * LEADS: componente_conferma_update
 *
 * PURPOSE:
 * - Permette la modifica di un componente esistente di un progetto hardware.
 * - Consente di aggiornare nome, descrizione, quantità e prezzo del componente.
 * - Mostra informazioni sul budget attuale e l'impatto potenziale delle modifiche.
 * - Reindirizza alla pagina di conferma prima dell'aggiornamento effettivo.
 */
-->

<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Modifica: <?= htmlspecialchars($componente_selezionato); ?></h3>
        </div>
        <div class="card-body">
            <?php
            // COSTO ATTUALE DEL COMPONENTE
            $costo_attuale = $componente_attuale['prezzo'] * $componente_attuale['quantita'];

            // CALCOLO COSTO TOTALE DEI COMPONENTI
            $costo_totale = 0;
            foreach ($componenti['data'] as $comp) {
                $costo_totale += $comp['prezzo'] * $comp['quantita'];
            }

            // SOTTRAGGO IL COSTO ATTUALE DEL COMPONENTE DAL COSTO TOTALE
            $costo_totale -= $costo_attuale;
            ?>

            <!-- ALERT BUDGET -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Informazioni Budget</h5>
                <p>Budget attuale del progetto: <strong><?= number_format($progetto['budget'], 2); ?>€</strong></p>
                <p>Costo totale attuale dei componenti: <strong><?= number_format($costo_totale, 2); ?>€</strong></p>
                <p>Costo attuale di questo componente: <strong><?= number_format($costo_attuale, 2); ?>€</strong></p>

                <hr>
                <p class="mb-0">
                    <strong>Nota:</strong> Se il costo totale dei componenti supera il budget attuale del progetto,
                    il budget verrà automaticamente incrementato per coprire il costo totale.
                </p>
            </div>

            <!-- AGGIORNA COMPONENTE ESISTENTE -->
            <form action="<?=generate_url('componente_conferma_update') ?>" method="post">
                <input type="hidden" name="nome_progetto"
                       value="<?= htmlspecialchars($_GET['nome']); ?>">
                <input type="hidden" name="nome_componente_originale"
                       value="<?= htmlspecialchars($componente_selezionato); ?>">

                <div class="mb-3">
                    <label for="nome_componente" class="form-label fw-bold">Nome Componente</label>
                    <input type="text" class="form-control" id="nome_componente" name="nome_componente" required
                           value="<?= htmlspecialchars($componente_selezionato); ?>">
                </div>

                <div class="mb-3">
                    <label for="descrizione" class="form-label fw-bold">Descrizione</label>
                    <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required><?= htmlspecialchars($componente_attuale['descrizione']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantita" class="form-label fw-bold">Quantità</label>
                        <input type="number" class="form-control" id="quantita" name="quantita" required min="1"
                               value="<?= htmlspecialchars($componente_attuale['quantita']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prezzo" class="form-label fw-bold">Prezzo Unitario (€)</label>
                        <input type="number" class="form-control" id="prezzo" name="prezzo" required step="0.01" min="0.01"
                               value="<?= htmlspecialchars($componente_attuale['prezzo']); ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'componenti', 'nome' => $_GET['nome']]); ?>"
                       class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-warning">Procedi</button>
                </div>
            </form>
        </div>
    </div>
</div>