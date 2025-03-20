<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_GET(['nome']);
$nome_progetto = $_GET['nome'];
$email = $_SESSION['email'];
$is_admin = $_SESSION['is_admin'];

// === CONTEXT ===
$context = [
    'collection' => 'PROGETTO',
    'action' => 'VIEW',
    'email' => $email,
    'redirect' => generate_url('progetti'),
    'in' => ['p_nome_progetto' => $nome_progetto]
];
$pipeline = new EventPipeline($context);

// === DATA ===
// DATI PROGETTO
$progetto = $pipeline->fetch('sp_progetto_select');
$progetto['tipo'] = $pipeline->fetch('sp_util_progetto_type')['tipo_progetto']; // TIPO
$photos = $pipeline->fetch_all('sp_foto_selectAll');                            // FOTO
$commenti = $pipeline->fetch_all('sp_commento_selectAll');                      // COMMENTI
$rewards = $pipeline->fetch_all('sp_reward_selectAllByProgetto');               // REWARD

// AFFIDABILITÀ
$in_affidabilita = ['p_email' => $progetto['email_creatore']];
$affidabilita = $pipeline->fetch('sp_util_creatore_get_affidabilita', $in_affidabilita)['affidabilita'];

// GIORNI RIMASTI ALLA SCADENZA
try {
    $today = new DateTime();
    $data_scadenza = new DateTime($progetto['data_limite']);
    $progetto['giorni_rimasti'] = ($today < $data_scadenza) ? $today->diff($data_scadenza)->days : 0;
} catch (Exception $e) {
    $progetto['giorni_rimasti'] = "Error";
}

// DATI FINANZIAMENTI
$progetto['tot_finanziamento'] = $pipeline->fetch('sp_finanziamento_selectSumByProgetto')['totale_finanziamenti']; // SOMMA
$progetto['percentuale'] = ($progetto['tot_finanziamento'] / $progetto['budget']) * 100;                                     // PERCENTUALE COMPLETAMENTO

// CONTROLLO SE L'UTENTE HA GIÀ FINANZIATO IL PROGETTO OGGI
$in = ['p_email' => $email, 'p_nome_progetto' => $progetto['nome']];
$finanziato_oggi = $pipeline->fetch('sp_util_utente_finanziato_progetto_oggi', $in)['finanziato_oggi'];

// SE HARDWARE, RECUPERO COMPONENTI
if ($progetto['tipo'] === 'HARDWARE') $componenti = $pipeline->fetch_all('sp_componente_selectAllByProgetto');

// SE SOFTWARE, RECUPERO PROFILI
if ($progetto['tipo'] === 'SOFTWARE') {
    // PROFILI E RELATIVE COMPETENZE
    $dati_profili = $pipeline->fetch_all('sp_profilo_selectAllByProgetto');

    // ORGANIZZAZIONE DATI PER PROFILO
    $profili = [];
    foreach ($dati_profili['data'] as $row) {
        if (!empty($row['competenza'])) {
            $profili[$row['nome_profilo']][] = [
                'competenza' => $row['competenza'],
                'livello' => $row['livello_richiesto']
            ];
        }
    }

    // PARTECIPANTI ACCETTATI
    $dati_partecipanti = $pipeline->fetch_all('sp_partecipante_selectAcceptedByProgetto');

    // ORGANIZZAZIONE DATI PARTECIPANTI
    $partecipanti_accettati = [];
    foreach ($dati_partecipanti['data'] as $row) {
        $partecipanti_accettati[$row['nome_profilo']] = [
            'email_utente' => $row['email_utente'],
            'nickname' => $row['nickname']
        ];
    }
}
?>

<!-- === PAGE === -->
<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <!-- ALERT -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- PROGETTO -->
        <div class="card mb-4 shadow-sm">
            <!-- NOME, TIPO, STATO -->
            <div class="card-header text-white bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0 fw-bolder"><?php echo htmlspecialchars($progetto['nome']); ?></h3>
                        <small class="text-light fw-bold"><?php echo strtoupper(htmlspecialchars($progetto['tipo'])); ?></small>
                    </div>
                    <span class="badge p-2 fs-4 <?php echo(strtolower(htmlspecialchars($progetto['stato'])) === 'chiuso' ? 'bg-danger' : 'bg-success'); ?>">
                    <?php echo strtoupper(htmlspecialchars($progetto['stato'])); ?>
                </span>
                </div>
            </div>

            <!-- CORPO -->
            <div class="card-body">
                <p class="fs-5">
                    <strong>Creatore:</strong> <?php echo htmlspecialchars($progetto['email_creatore']); ?>
                    (Affidabilità: <?php echo htmlspecialchars($affidabilita); ?>%)
                </p>
                <hr>
                <div class="card mb-3">
                    <div class="card-header d-inline-flex align-items-center justify-content-between">
                        <p class="fw-bold fs-5">Descrizione</p>
                        <?php if (is_progetto_owner($email, $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                            <form action="<?php echo htmlspecialchars(generate_url('progetto_aggiorna', ['attr' => 'descrizione', 'nome' => $progetto['nome']])); ?>"
                                  method="post">
                                <button type="submit" class="btn btn-warning">Modifica</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <!-- DESCRIZIONE & FOTO -->
                    <div class="card-body">
                        <?php if (!empty($progetto['descrizione'])): ?>
                            <p><?php echo htmlspecialchars($progetto['descrizione']); ?></p>
                        <?php else: ?>
                            <p>Nessuna descrizione disponibile per questo progetto.</p>
                        <?php endif; ?>
                        <hr>
                        <?php if ($photos['failed']): ?>
                            <p class="text-danger">Errore durante il recupero delle foto.</p>
                        <?php elseif (empty($photos['data'])): ?>
                            <p>Nessuna foto disponibile per questo progetto.</p>
                        <?php else: ?>
                            <p class="text-muted small"><?php if (count($photos['data']) > 4): ?>(Scorri per visualizzare le restanti)<?php endif; ?></p>
                            <div class="card-body">
                                <div class="d-flex flex-nowrap overflow-auto">
                                    <?php foreach ($photos['data'] as $photo): ?>
                                        <div class="flex-shrink-0 w-25 p-2">
                                            <?php $base64 = base64_encode($photo['foto']); ?>
                                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                 class="img-fluid rounded"
                                                 alt="Foto progetto">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- DATA INSERIMENTO, SCADENZA, GIORNI RIMASTI -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column justify-content-center fw-bold my-2 fs-5">
                    Durata: <?php echo htmlspecialchars(date('d/m/Y', strtotime($progetto['data_inserimento']))); ?>
                    - <?php echo htmlspecialchars(date('d/m/Y', strtotime($progetto['data_limite']))); ?>
                </div>
                <div>
                    <?php if ($progetto['stato'] === 'aperto'): ?>
                        <span class="badge bg-dark-subtle text-dark-emphasis fs-6 fw-bold">
                            <?php echo htmlspecialchars($progetto['giorni_rimasti']); ?> GIORNI RIMASTI
                        </span>
                    <?php else: ?>
                        <span class="badge bg-dark-subtle text-dark-emphasis">TERMINATO</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr>

        <!-- FINANZIAMENTI / BUDGET -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header fs-5 d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <strong>Finanziamenti</strong>
                    <small class="text-muted fs-6">
                        Finanzia il progetto per aiutare il creatore a raggiungere il budget richiesto.
                        Ogni finanziamento è ricompensato con una delle reward disponibili.
                    </small>
                </div>
                <?php if (is_progetto_owner($email, $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                    <form action="<?php echo htmlspecialchars(generate_url('progetto_aggiorna', ['attr' => 'budget', 'nome' => $progetto['nome']])); ?>"
                          method="post">
                        <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- BUDGET, SOMMA FINANZIAMENTI, COMPLETAMENTO BUDGET -->
            <div class="card-body">
                <!-- BUDGET -->
                <div class="bg-secondary-subtle p-1 rounded text-center">
                    <p class="fs-4">
                        <strong>Budget:</strong> <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                    </p>
                </div>
                <hr>
                <!-- PERCENTUALE COMPLETAMENTO BUDGET -->
                <div class="d-flex w-100 fw-bold justify-content-center fs-5">
                    <?php echo round($progetto['percentuale'], 2); ?>%
                </div>

                <!-- BARRA COMPLETAMENTO BUDGET -->
                <div class="progress my-2 position-relative" style="height: 40px;">
                    <div class="progress-bar fw-bold bg-success"
                         style="width: <?php echo round($progetto['percentuale'], 2); ?>%; height: 100%;">
                    </div>
                    <div class="position-absolute top-50 start-50 translate-middle text-center fw-bold text-black fs-6">
                        <?php echo htmlspecialchars(number_format($progetto['tot_finanziamento'], 2)); ?>€
                        / <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                    </div>
                </div>

                <hr class="my-4">

                <!-- REWARDS -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="fs-5 d-flex flex-column">
                            <strong>Reward</strong>
                            <small class="text-muted fs-6">
                                <?php if (count($rewards['data']) > 4): ?>(Scorri per visualizzare le restanti)<?php endif; ?>
                                Visualizza le reward disponibili per il progetto. Ogni reward è ottenibile con un
                                finanziamento di un certo importo.
                            </small>
                        </div>
                        <?php if (is_progetto_owner($email, $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                            <form action="<?php echo htmlspecialchars(generate_url('progetto_aggiorna', ['attr' => 'rewards', 'nome' => $progetto['nome']])); ?>"
                                  method="post">
                                <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- LISTA REWARD -->
                    <div class="card-body">
                        <div class="d-flex flex-nowrap overflow-auto">
                            <?php if ($rewards['failed']): ?>
                                <p class="text-danger">Errore durante il recupero delle reward.</p>
                            <?php elseif (empty($rewards['data'])): ?>
                                <p>Nessuna reward disponibile per questo progetto.</p>
                            <?php else: ?>
                                <?php foreach ($rewards['data'] as $reward): ?>
                                    <div class="flex-shrink-0 w-25 p-2">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header">
                                                <p class="fw-bold"><?php echo htmlspecialchars($reward['codice']); ?></p>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <p class="fw-bold">
                                                    Importo minimo:
                                                    <?php echo htmlspecialchars(number_format($reward['min_importo'], 2)); ?>€
                                                </p>
                                                <p class="flex-grow-1"><?php echo htmlspecialchars($reward['descrizione']); ?></p>
                                                <!-- FOTO REWARD -->
                                                <div class="d-flex justify-content-center mt-auto">
                                                    <?php $base64 = base64_encode($reward['foto']); ?>
                                                    <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                         class="img-fluid rounded"
                                                         alt="Foto reward">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INSERIMENTO FINANZIAMENTO -->
            <?php if ($progetto['stato'] === 'aperto'): ?>
                <?php if (!$finanziato_oggi): ?>
                    <div class="card-footer">
                        <form action="<?php echo htmlspecialchars(generate_url('finanziamento_conferma')); ?>"
                              method="post">
                            <input type="hidden" name="nome" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                            <div class="form-group mt-2">
                                <label class="fs-5 mb-2 fw-bold" for="importo">Finanzia il Progetto (€)</label>
                                <p class="small text-muted">Inserisci l'importo che desideri finanziare e premi Invia.</p>
                                <input type="number" class="form-control mb-2" id="importo" name="importo"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="150.00"
                                       required>
                                <button type="submit" class="btn btn-primary my-2">Invia</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card-footer fw-bold fs-5">
                        <p>Hai già finanziato il progetto oggi. Ritorna domani.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card-footer fw-bold fs-5">
                    <p>Il progetto è chiuso ai finanziamenti.</p>
                </div>
            <?php endif; ?>
        </div>

        <hr>

        <!-- PROFILI / COMPONENTI -->
        <div class="card mt-4">
            <div class="card-header fs-5 d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <?php if ($progetto['tipo'] === 'SOFTWARE'): ?>
                        <strong>Profili</strong>
                        <small class="text-muted fs-6">
                            <?php if (count($profili) > 4): ?>(Scorri per visualizzare i restanti)<?php endif; ?>
                            Seleziona un profilo per candidarti al progetto software. Assicurati di avere le competenze
                            e il livello richiesto (X/5).
                        </small>
                    <?php else: ?>
                        <strong>Componenti</strong>
                        <small class="text-muted fs-6">
                            <?php if (count($componenti['data']) > 4): ?>(Scorri per visualizzare i restanti)<?php endif; ?>
                            Di seguito i componenti richiesti per il progetto hardware.
                        </small>
                    <?php endif; ?>
                </div>
                <?php if (is_progetto_owner($email, $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                    <?php
                    $tipo = ($progetto['tipo'] === 'SOFTWARE') ? 'profili' : 'componenti';
                    $nome_progetto = htmlspecialchars($progetto['nome']);
                    ?>
                    <form action="<?php echo htmlspecialchars(generate_url('progetto_aggiorna', ['attr' => $tipo, 'nome' => $nome_progetto])); ?>"
                          method="post">
                        <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-nowrap overflow-auto">
                <?php if ($progetto['tipo'] === 'SOFTWARE'): ?>
                    <?php if ($dati_profili['failed']): ?>
                        <p class="text-danger">Errore durante il recupero dei profili.</p>
                    <?php elseif (empty($profili)): ?>
                        <p>Profili non disponibili per questo progetto.</p>
                    <?php else: ?>
                        <?php foreach ($profili as $nome_profilo => $skills): ?>
                            <div class="flex-shrink-0 w-25 p-2">
                                <div class="card shadow-sm h-100 d-flex flex-column">
                                    <div class="card-header">
                                        <p class="fw-bold mb-0"><?php echo htmlspecialchars($nome_profilo); ?></p>
                                    </div>
                                    <div class="card-body overflow-auto flex-grow-1">
                                        <?php if (!empty($skills)): ?>
                                            <ul>
                                                <?php foreach ($skills as $skill): ?>
                                                    <li>
                                                        <?php echo htmlspecialchars($skill['competenza']); ?>
                                                        (<?php echo htmlspecialchars($skill['livello']); ?>/5)
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-center text-muted">Nessuna competenza attualmente associata.</p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($progetto['stato'] === 'aperto'): ?>
                                        <div class="card-footer">
                                            <?php if (!is_progetto_owner($email, $progetto['nome'])): ?>
                                                <?php
                                                // STATO INIZIALE CANDIDATURA
                                                $utente_ha_candidatura = false;
                                                $utente_rifiutato = false;
                                                $utente_idoneo = false;
                                                $profilo_occupato = isset($partecipanti_accettati[$nome_profilo]);

                                                // VERIFICO SOLO SE IL PROFILO NON È GIÀ OCCUPATO
                                                if (!$profilo_occupato) {
                                                    // VERIFICO LO STATO DELLA CANDIDATURA
                                                    $in = [
                                                        'p_email_utente' => $email,
                                                        'p_nome_progetto' => $progetto['nome'],
                                                        'p_nome_profilo' => $nome_profilo
                                                    ];

                                                    $stato_result = $pipeline->fetch('sp_partecipante_getStatus', $in);

                                                    if (!empty($stato_result)) {
                                                        $stato = $stato_result['stato'] ?? '';
                                                        if ($stato === 'potenziale') {
                                                            $utente_ha_candidatura = true;
                                                        } elseif ($stato === 'rifiutato') {
                                                            $utente_rifiutato = true;
                                                        }
                                                    }

                                                    // VERIFICO IDONEITÀ SOLO SE NON HA GIÀ CANDIDATURE
                                                    if (!$utente_ha_candidatura && !$utente_rifiutato) {
                                                        $idoneita_result = $pipeline->fetch('sp_util_partecipante_is_eligible', $in);
                                                        $utente_idoneo = !empty($idoneita_result) && $idoneita_result['eligible'];
                                                    }
                                                }
                                                ?>

                                                <?php if ($profilo_occupato): ?>
                                                    <button class="btn btn-secondary w-100" disabled>
                                                        Occupato da <?php echo htmlspecialchars($partecipanti_accettati[$nome_profilo]['nickname']); ?>
                                                    </button>
                                                <?php elseif ($utente_ha_candidatura): ?>
                                                    <button class="btn btn-warning w-100" disabled>Candidatura in attesa</button>
                                                <?php elseif ($utente_rifiutato): ?>
                                                    <button class="btn btn-danger w-100" disabled>Candidatura rifiutata</button>
                                                <?php elseif (!$utente_idoneo): ?>
                                                    <button class="btn btn-secondary w-100" disabled>Non idoneo</button>
                                                <?php else: ?>
                                                    <form action="../actions/candidatura_insert.php" method="post">
                                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                                        <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($nome_profilo); ?>">
                                                        <button type="submit" class="btn btn-primary w-100">Candidati</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($componenti['failed']): ?>
                        <p class="text-danger">Errore durante il recupero dei componenti.</p>
                    <?php elseif (empty($componenti['data'])): ?>
                        <p>Componenti non disponibili per questo progetto.</p>
                    <?php else: ?>
                        <?php foreach ($componenti['data'] as $componente): ?>
                            <div class="flex-shrink-0 w-25 p-2">
                                <div class="card shadow-sm h-100 d-flex flex-column">
                                    <div class="card-header">
                                        <p class="fw-bold"><?php echo htmlspecialchars($componente['nome_componente']); ?></p>
                                    </div>
                                    <div class="card-body overflow-auto flex-grow-1">
                                        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($componente['descrizione']); ?></p>
                                        <p><strong>Quantità:</strong> <?php echo htmlspecialchars($componente['quantita']); ?></p>
                                        <p><strong>Prezzo:</strong> <?php echo htmlspecialchars(number_format($componente['prezzo'], 2)); ?>€</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <!-- COMMENTI -->
        <div class="card mt-4">
            <div class="card-header fs-5 d-flex flex-column">
                <strong>Commenti</strong>
                <small class="text-muted fs-6">
                    Lascia un commento per esprimere la tua opinione sul progetto.
                </small>
            </div>
            <div class="card-body overflow-auto" style="max-height: 500px;">
                <?php if ($commenti['failed']): ?>
                    <p class="text-danger">Errore durante il recupero dei commenti.</p>
                <?php elseif (empty($commenti['data'])): ?>
                    <p>Nessun commento disponibile per questo progetto.</p>
                <?php else: ?>
                    <?php foreach ($commenti['data'] as $commento): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>
                                    <?php echo htmlspecialchars($commento['nickname']); ?>
                                    <?php if ($commento['email_utente'] === $progetto['email_creatore']): ?>
                                        (Creatore)
                                    <?php endif; ?>
                                    <?php if ($commento['email_utente'] === $email): ?>
                                        (You)
                                    <?php endif; ?>
                                </strong>
                                <!-- DATA COMMENTO -->
                                <small class="text-muted mx-2"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($commento['data']))); ?></small>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($commento['testo']); ?></p>
                            </div>
                            <!-- BOTTONE ELIMINA COMMENTO -->
                            <?php if ($commento['email_utente'] === $email || $is_admin): ?>
                                <div class="card-footer">
                                    <form action="../actions/commento_delete.php" method="post">
                                        <input type="hidden" name="id_commento" value="<?php echo htmlspecialchars($commento['id']); ?>">
                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                        <input type="hidden" name="email_utente" value="<?php echo htmlspecialchars($commento['email_utente']); ?>">
                                        <button type="submit" class="btn btn-danger">Elimina</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            <!-- RISPOSTA COMMENTO -->
                            <?php if (!empty($commento['risposta'])): ?>
                                <div class="card-footer">
                                    <strong>
                                        Risposta
                                        (<?php if (is_progetto_owner($email, $progetto['nome'])): ?>You<?php else: ?>Creatore<?php endif; ?>)
                                    </strong>
                                    <p><?php echo htmlspecialchars($commento['risposta']); ?></p>
                                    <?php if (is_progetto_owner($email, $progetto['nome']) || $is_admin): ?>
                                        <form action="../actions/commento_risposta_delete.php" method="post">
                                            <input type="hidden" name="id_commento" value="<?php echo htmlspecialchars($commento['id']); ?>">
                                            <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                            <button type="submit" class="btn btn-danger">Elimina</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php elseif (is_progetto_owner($email, $progetto['nome'])): ?>
                                <div class="card-footer">
                                    <form action="../actions/commento_risposta_insert.php" method="post">
                                        <input type="hidden" name="id_commento" value="<?php echo htmlspecialchars($commento['id']); ?>">
                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                        <div class="form-group mt-2">
                                            <label for="risposta">Rispondi</label>
                                            <textarea class="form-control" id="risposta" name="risposta" rows="1" required></textarea>
                                            <button type="submit" class="btn btn-primary mt-2">Invia</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- INSERIMENTO COMMENTO -->
            <div class="card-footer">
                <form action="../actions/commento_insert.php" method="post">
                    <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                    <div class="form-group">
                        <label class="fs-5 my-2 fw-bold" for="commento">Commento</label>
                        <p class="small text-muted">Inserisci un commento per esprimere la tua opinione sul progetto.</p>
                        <textarea class="form-control my-2" id="commento" name="commento" rows="3" required></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Invia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require '../components/footer.php'; ?>