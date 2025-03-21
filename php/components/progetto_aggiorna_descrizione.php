<?php
// Recupero le foto del progetto
$in = ['p_nome_progetto' => $_GET['nome']];
$photos = $pipeline->fetch_all('sp_foto_selectAll', $in);
?>

<!-- AGGIORNA DESCRIZIONE -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Aggiorna Descrizione</h3>
    </div>
    <div class="card-body">
        <form action="../actions/progetto_descrizione_update.php" method="post">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($_GET['nome']); ?>">
            <div class="form-group">
                <label for="descrizione" class="fw-bold fs-5">Nuova Descrizione</label>
                <textarea class="form-control my-3" id="descrizione" name="descrizione" rows="5"
                          required><?= htmlspecialchars($progetto['descrizione']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Aggiorna</button>
        </form>
    </div>
</div>

<hr>

<!-- INSERISCI/ELIMINA FOTO -->
<div class="card mt-3">
    <div class="card-header bg-primary text-white">
        <h3>Inserisci/Elimina Foto</h3>
    </div>
    <div class="card-body">
        <?php if ($photos['failed']): ?>
            <div class="alert alert-danger" role="alert">
                Errore nel recupero delle foto.
            </div>
        <?php elseif (empty($photos['data'])): ?>
            <div class="alert alert-info" role="alert">
                Nessuna foto presente.
            </div>
        <?php else: ?>
            <div class="card-body">
                <div class="d-flex flex-nowrap overflow-auto">
                    <?php foreach ($photos['data'] as $photo): ?>
                        <div class="flex-shrink-0 w-25 px-2">
                            <?php $base64 = base64_encode($photo['foto']); ?>
                            <form action="../actions/foto_delete.php" method="post">
                                <input type="hidden" name="nome" value="<?= $progetto['nome']; ?>">
                                <input type="hidden" name="id" value="<?= $photo['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm mb-2">Elimina</button>
                            </form>
                            <img src="data:image/jpeg;base64,<?= $base64; ?>"
                                 class="img-fluid rounded"
                                 alt="Foto progetto">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <hr>
        <!-- INSERIMENTO FOTO -->
        <form action="../actions/foto_insert.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($_GET['nome']); ?>">
            <div class="form-group">
                <label for="foto" class="fw-bold fs-5">Seleziona Foto (Max 4MB)</label>
                <p class="small text-muted">Insersci una foto per il progetto.</p>
                <input type="file" class="form-control my-3" id="foto" name="foto" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Inserisci Foto</button>
        </form>
    </div>
</div>