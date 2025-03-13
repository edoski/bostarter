<div class="col-md-4">
    <div class="card h-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Componenti Esistenti</h3>
            <?php if (isset($_GET['componente'])): ?>
                <a href="?attr=componenti&nome=<?php echo urlencode($_GET['nome']); ?>"
                   class="btn btn-success">Nuovo Componente</a>
            <?php endif; ?>
        </div>
        <div class="card-body overflow-y-auto">
            <?php if (empty($componenti)): ?>
                <p class="text-center">Nessun componente esistente</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($componenti as $componente): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo htmlspecialchars($componente['nome_componente']); ?></h6>
                                <div>
                                    <!-- Link per la modifica -->
                                    <a href="?attr=componenti&nome=<?php echo urlencode($_GET['nome']); ?>&componente=<?php echo urlencode($componente['nome_componente']); ?>"
                                       class="btn btn-sm btn-primary">
                                        Modifica
                                    </a>
                                    <form action="../actions/componente_delete.php" method="post" class="d-inline">
                                        <input type="hidden" name="nome_progetto"
                                               value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                                        <input type="hidden" name="nome_componente"
                                               value="<?php echo htmlspecialchars($componente['nome_componente']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo componente?')">
                                            Elimina
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="text-muted small">
                                    <strong>Descrizione:</strong> <?php echo htmlspecialchars($componente['descrizione']); ?>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="badge bg-info text-dark me-1 mb-1">
                                        Quantità: <?php echo htmlspecialchars($componente['quantita']); ?>
                                    </span>
                                    <span class="badge bg-info text-dark me-1 mb-1">
                                        Prezzo: <?php echo htmlspecialchars(number_format($componente['prezzo'], 2)); ?>€
                                    </span>
                                    <span class="badge bg-warning text-dark me-1 mb-1">
                                        Totale: <?php echo htmlspecialchars(number_format($componente['prezzo'] * $componente['quantita'], 2)); ?>€
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>