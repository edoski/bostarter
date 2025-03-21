<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <?= empty($componente_selezionato) ? "Nuovo Componente" : "Modifica: " . htmlspecialchars($componente_selezionato); ?>
            </h3>
        </div>
        <div class="card-body">
            <?php
            // CALCOLO COSTO ATTUALE DEL COMPONENTE (SE IN MODIFICA)
            $costo_attuale = 0;
            if (!$nuovo_componente) {
                $costo_attuale = $componente_corrente['prezzo'] * $componente_corrente['quantita'];
            }

            // CALCOLO COSTO TOTALE DEI COMPONENTI
            $costo_totale = 0;
            foreach ($componenti['data'] as $comp) {
                $costo_totale += $comp['prezzo'] * $comp['quantita'];
            }

            // SE IN MODIFICA, SOTTRAGGO IL COSTO ATTUALE DEL COMPONENTE DAL COSTO TOTALE
            if (!$nuovo_componente) {
                $costo_totale -= $costo_attuale;
            }
            ?>

            <!-- ALERT BUDGET -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Informazioni Budget</h5>
                <p>Budget attuale del progetto: <strong><?= number_format($progetto['budget'], 2); ?>€</strong></p>
                <p>Costo totale attuale dei componenti: <strong><?= number_format($costo_totale, 2); ?>€</strong></p>

                <?php if (!$nuovo_componente): ?>
                    <p>Costo attuale di questo componente: <strong><?= number_format($costo_attuale, 2); ?>€</strong></p>
                <?php endif; ?>

                <hr>
                <p class="mb-0">
                    <strong>Nota:</strong> Se il costo totale dei componenti supera il budget attuale del progetto,
                    il budget verrà automaticamente incrementato per coprire il costo totale.
                </p>
            </div>

            <?php if ($nuovo_componente): ?>
                <!-- CREA NUOVO COMPONENTE -->
                <form action="../public/componente_conferma_insert.php" method="post">
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
            <?php else: ?>
                <!-- AGGIORNA COMPONENTE ESISTENTE -->
                <form action="../public/componente_conferma_update.php" method="post">
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
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required><?= htmlspecialchars($componente_corrente['descrizione']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantita" class="form-label fw-bold">Quantità</label>
                            <input type="number" class="form-control" id="quantita" name="quantita" required min="1"
                                   value="<?= htmlspecialchars($componente_corrente['quantita']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prezzo" class="form-label fw-bold">Prezzo Unitario (€)</label>
                            <input type="number" class="form-control" id="prezzo" name="prezzo" required step="0.01" min="0.01"
                                   value="<?= htmlspecialchars($componente_corrente['prezzo']); ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?attr=componenti&nome=<?= urlencode($_GET['nome']); ?>" class="btn btn-secondary">Annulla</a>
                        <button type="submit" class="btn btn-warning">Procedi</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>