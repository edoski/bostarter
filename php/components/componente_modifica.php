<div class="col-md-8">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <?php echo empty($componenteSelezionato) ? "Nuovo Componente" : "Modifica: " . htmlspecialchars($componenteSelezionato); ?>
            </h3>
        </div>
        <div class="card-body">
            <?php
            // Calcola il costo attuale del componente (se in modifica)
            $costo_attuale = 0;
            if (!$nuovo_componente) {
                $costo_attuale = $componenteCorrente['prezzo'] * $componenteCorrente['quantita'];
            }

            // Calcola il costo totale attuale di tutti i componenti
            $costo_totale = 0;
            foreach ($componenti as $comp) {
                $costo_totale += $comp['prezzo'] * $comp['quantita'];
            }

            // Se stiamo modificando, sottraiamo il costo attuale del componente che stiamo modificando
            if (!$nuovo_componente) {
                $costo_totale -= $costo_attuale;
            }
            ?>

            <!-- Avviso Budget -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Informazioni Budget</h5>
                <p>Budget attuale del progetto: <strong><?php echo number_format($budget_progetto, 2); ?>€</strong></p>
                <p>Costo totale attuale dei componenti: <strong><?php echo number_format($costo_totale, 2); ?>€</strong></p>

                <?php if (!$nuovo_componente): ?>
                    <p>Costo attuale di questo componente: <strong><?php echo number_format($costo_attuale, 2); ?>€</strong></p>
                <?php endif; ?>

                <hr>
                <p class="mb-0">
                    <strong>Nota:</strong> Se il costo totale dei componenti supera il budget attuale del progetto,
                    il budget verrà automaticamente incrementato per coprire il costo totale.
                </p>
            </div>

            <?php if ($nuovo_componente): ?>
                <!-- Form per creare un nuovo componente -->
                <form action="../actions/componente_insert.php" method="post">
                    <input type="hidden" name="nome_progetto"
                           value="<?php echo htmlspecialchars($_GET['nome']); ?>">

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
                <!-- Form per aggiornare un componente esistente -->
                <form action="../public/componente_update_conferma.php" method="post">
                    <input type="hidden" name="nome_progetto"
                           value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                    <input type="hidden" name="nome_componente_originale"
                           value="<?php echo htmlspecialchars($componenteSelezionato); ?>">

                    <div class="mb-3">
                        <label for="nome_componente" class="form-label fw-bold">Nome Componente</label>
                        <input type="text" class="form-control" id="nome_componente" name="nome_componente" required
                               value="<?php echo htmlspecialchars($componenteSelezionato); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="descrizione" class="form-label fw-bold">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required><?php echo htmlspecialchars($componenteCorrente['descrizione']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantita" class="form-label fw-bold">Quantità</label>
                            <input type="number" class="form-control" id="quantita" name="quantita" required min="1"
                                   value="<?php echo htmlspecialchars($componenteCorrente['quantita']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prezzo" class="form-label fw-bold">Prezzo Unitario (€)</label>
                            <input type="number" class="form-control" id="prezzo" name="prezzo" required step="0.01" min="0.01"
                                   value="<?php echo htmlspecialchars($componenteCorrente['prezzo']); ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?attr=componenti&nome=<?php echo urlencode($_GET['nome']); ?>" class="btn btn-secondary">Annulla</a>
                        <button type="submit" class="btn btn-warning">Procedi</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>