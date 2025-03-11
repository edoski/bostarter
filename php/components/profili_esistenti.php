<div class="col-md-4">
    <div class="card h-100">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Profili Esistenti</h5>
        </div>
        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <?php if (empty($profili)): ?>
                <p class="text-center">Nessun profilo esistente</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach (array_keys($profili) as $nomeProfilo): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo htmlspecialchars($nomeProfilo); ?></h6>
                                <div>
                                    <!-- Link per la modifica -->
                                    <a href="?attr=profilo&nome=<?php echo urlencode($_GET['nome']); ?>&profilo=<?php echo urlencode($nomeProfilo); ?>"
                                       class="btn btn-sm btn-primary">
                                        Modifica
                                    </a>
                                    <form action="../actions/profilo_delete.php" method="post" class="d-inline">
                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($_GET['nome']); ?>">
                                        <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($nomeProfilo); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo profilo? Tutte le candidature associate verranno rimosse.')">
                                            Elimina
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <?php if (!empty($profili[$nomeProfilo])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Competenze:</small>
                                    <div class="mt-1">
                                        <?php foreach ($profili[$nomeProfilo] as $competenza): ?>
                                            <span class="badge bg-info text-dark me-1 mb-1">
                                                <?php echo htmlspecialchars($competenza['competenza']); ?>
                                                (<?php echo htmlspecialchars($competenza['livello']); ?>)
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>