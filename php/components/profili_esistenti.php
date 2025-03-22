<div class="col-md-4">
    <div class="card h-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Profili Esistenti</h3>
            <?php if (isset($_GET['profilo'])): ?>
                <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $_GET['nome']]); ?>"
                   class="btn btn-success">Nuovo Profilo</a>
            <?php endif; ?>
        </div>
        <div class="card-body overflow-auto" style="max-height: 500px;">
            <?php if (empty($profili)): ?>
                <p class="text-center">Nessun profilo esistente</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach (array_keys($profili) as $nome_profilo): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?= htmlspecialchars($nome_profilo); ?></h6>
                                <div>
                                    <a href="<?=generate_url('progetto_aggiorna', ['attr' => 'profili', 'nome' => $_GET['nome'], 'profilo' => $nome_profilo]); ?>"
                                       class="btn btn-sm btn-primary">
                                        Modifica
                                    </a>
                                    <form action="<?=generate_url('profilo_delete') ?>" method="post" class="d-inline">
                                        <input type="hidden" name="nome_progetto" value="<?= htmlspecialchars($_GET['nome']); ?>">
                                        <input type="hidden" name="nome_profilo" value="<?= htmlspecialchars($nome_profilo); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo profilo? Tutte le candidature associate verranno rimosse.')">
                                            Elimina
                                        </button>
                                    </form>
                                </div>
                            </div>

                                <?php if (count($profili[$nome_profilo]) > 0): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Competenze:</small>
                                        <div class="mt-1">
                                            <?php foreach ($profili[$nome_profilo] as $competenza): ?>
                                                <span class="badge bg-info text-dark me-1 mb-1">
                                                <?= htmlspecialchars($competenza['competenza']); ?>
                                                (<?= htmlspecialchars($competenza['livello']); ?>)
                                            </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Nessuna competenza associata</small>
                                    </div>
                                <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>