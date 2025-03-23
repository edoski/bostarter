<?php
/**
 * COMPONENT: progetto_aggiorna_reward (PARENT: progetto_aggiorna)
 *
 * ACTIONS: reward_insert
 *
 * PURPOSE:
 * - Gestisce le ricompense (rewards) associate a un progetto.
 * - Visualizza le rewards esistenti con le relative informazioni.
 * - Permette l'inserimento di nuove rewards con codice, importo minimo, descrizione e foto.
 */

// === VARIABLES ===
check_GET(['nome']);
$nome_progetto = $_GET['nome'];

// === DATA ===
// RECUPERO REWARD DEL PROGETTO
$in = ['p_nome_progetto' => $nome_progetto];
$rewards = $pipeline->fetch_all('sp_reward_selectAllByProgetto', $in);
?>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h3>Gestione Reward</h3>
    </div>
    <div class="card-body">
        <!-- LISTA REWARD -->
        <h4 class="mb-3">Reward Esistenti</h4>
        <?php if ($rewards['failed']): ?>
            <p class="alert alert-danger">Errore durante il recupero delle reward.</p>
        <?php elseif (empty($rewards['data'])): ?>
            <p class="alert alert-warning">Nessuna reward definita per questo progetto. Aggiungi almeno la reward RWD_Default.</p>
        <?php else: ?>
            <div class="d-flex flex-nowrap overflow-auto mb-4 h-100">
                <?php foreach ($rewards['data'] as $reward): ?>
                    <div class="flex-shrink-0 w-25 p-2">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <p class="fw-bold"><?= htmlspecialchars($reward['codice']); ?></p>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="fw-bold">
                                    Importo minimo:
                                    <?= htmlspecialchars(number_format($reward['min_importo'], 2)); ?>€
                                </p>
                                <p class="flex-grow-1"><?= htmlspecialchars($reward['descrizione']); ?></p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <?php $base64 = base64_encode($reward['foto']); ?>
                                    <img src="data:image/jpeg;base64,<?= $base64; ?>"
                                         class="img-fluid rounded"
                                         alt="Foto reward">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <hr>

        <!-- INSERIMENTO NUOVO REWARD -->
        <h4 class="mb-3">Aggiungi Nuova Reward</h4>
        <form action="<?=generate_url('reward_insert') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($nome_progetto); ?>">

            <!-- CODICE REWARD E IMPORTO MINIMO -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codice" class="form-label fw-bold">Codice Reward</label>
                    <p class="small text-muted">Inserisci un codice univoco che identifichi questa reward.</p>
                    <input type="text" class="form-control" id="codice" name="codice" required
                           placeholder="RWD_Default">
                </div>
                <div class="col-md-6">
                    <label for="min_importo" class="form-label fw-bold">Importo Minimo (€)</label>
                    <p class="small text-muted">Importo minimo per ottenere questa reward.</p>
                    <input type="number" class="form-control" id="min_importo" name="min_importo"
                           step="0.01" min="0.01" required placeholder="0.01">
                </div>
            </div>

            <!-- DESCRIZIONE -->
            <div class="mb-3">
                <label for="descrizione" class="form-label fw-bold">Descrizione Reward</label>
                <p class="small text-muted">Descrivi cosa riceverà l'utente con questa reward.</p>
                <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required
                          placeholder="Descrizione della reward..."></textarea>
            </div>

            <!-- FOTO REWARD -->
            <div class="mb-3">
                <label for="foto" class="form-label fw-bold">Foto Reward</label>
                <p class="small text-muted">Carica un'immagine per rappresentare questa reward (Max 4MB).</p>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
            </div>

            <!-- SUBMIT -->
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Sei sicuro di voler inserire questa reward?')">
                Inserisci Reward
            </button>
        </form>
    </div>
</div>