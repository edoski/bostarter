<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// 1. L'utente ha effettuato il login
checkAuth();

// 2. È stato selezionato un progetto valido
if (!isset($_GET['nome'])) {
    redirect(
        false,
        "Errore selezionamento progetto. Riprova.",
        '../public/progetti.php'
    );
}

// === DATABASE ===
// Recupero i dettagli del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    // sp_progetto_select ritorna un insieme di record, di cui il primo (e unico) si rappresenta come l'array del progetto
    $progetto = sp_invoke('sp_progetto_select', $in)[0];

    // Controllo se il progetto esiste
    if (!isset($progetto)) {
        redirect(
            false,
            "Progetto non trovato.",
            '../public/progetti.php'
        );
    }
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del progetto: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Recupero il tipo del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    // Restituisce un array di record, di cui il primo (e unico) si rappresenta come il campo testo 'tipo_progetto'
    $progetto['tipo'] = sp_invoke('sp_util_progetto_type', $in)[0]['tipo_progetto'] ?? '';
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del tipo del progetto: " . $ex->errorInfo[2],
        "../public/progetti.php"
    );
}

// Recupero l'affidabilità del creatore
try {
    $in = ['p_email' => $progetto['email_creatore']];
    $affidabilita = sp_invoke('sp_util_creatore_get_affidabilita', $in)[0]['affidabilita'];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dell'affidabilità: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Calcolo i giorni rimasti alla scadenza del progetto
$today = new DateTime();
try {
    $scadenzaDate = new DateTime($progetto['data_limite']);
    $progetto['giorni_rimasti'] = ($today < $scadenzaDate) ? $today->diff($scadenzaDate)->days : 0;
} catch (DateMalformedStringException $e) {
    $progetto['giorni_rimasti'] = "Errore";
}

// Recupero le foto del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $photos = sp_invoke('sp_foto_selectAll', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero delle foto: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Recupero il totale dei finanziamenti per il progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    // Restituisce un array di record, di cui il primo (e unico) si rappresenta come il campo numerico 'totale_finanziamenti'
    $totalFin = sp_invoke('sp_finanziamento_selectSumByProgetto', $in)[0]['totale_finanziamenti'] ?? 0;

    $progetto['tot_finanziamento'] = $totalFin;
    $budget = $progetto['budget'];
    $progetto['percentuale'] = ($budget > 0) ? ($totalFin / $budget) * 100 : 0;
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero del totale dei finanziamenti: " . $ex->errorInfo[2],
        "../public/progetti.php"
    );
}

// Recupero le reward del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $rewards = sp_invoke('sp_reward_selectAllByProgetto', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero delle reward: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Se il progetto è di tipo SOFTWARE, recupero i profili
if ($progetto['tipo'] === 'SOFTWARE') {
    try {
        $in = ['p_nome_progetto' => $_GET['nome']];
        $profili = [];
        $result = sp_invoke('sp_profilo_selectAllByProgetto', $in);

        foreach ($result as $row) {
            $profili[$row['nome_profilo']][] = [
                'competenza' => $row['competenza'],
                'livello' => $row['livello_richiesto']
            ];
        }

        // Filtra le entry vuote (accade quando viene appena creato un profilo, prima di aggiungere competenze)
        foreach ($profili as $profiloName => &$competenze) {
            $competenze = array_filter($competenze, function($item) {
                return !empty($item['competenza']);
            });
        }

        // Recupero i partecipanti accettati
        $acceptedResult = sp_invoke('sp_partecipante_selectAcceptedByProgetto', $in);
        foreach ($acceptedResult as $row) {
            $acceptedParticipants[$row['nome_profilo']] = [
                'email_utente' => $row['email_utente'],
                'nickname' => $row['nickname']
            ];
        }
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero dei profili: " . $ex->errorInfo[2],
            '../public/progetti.php'
        );
    }
} else { // Altrimenti, il progetto è di tipo HARDWARE e recupero i componenti
    try {
        $in = ['p_nome_progetto' => $_GET['nome']];
        $componenti = sp_invoke('sp_componente_selectAllByProgetto', $in);
    } catch (PDOException $ex) {
        redirect(
            false,
            "Errore durante il recupero dei componenti: " . $ex->errorInfo[2],
            '../public/progetti.php'
        );
    }
}

// Recupero i commenti del progetto
try {
    $in = ['p_nome_progetto' => $_GET['nome']];
    $commenti = sp_invoke('sp_commento_selectAll', $in);
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il recupero dei commenti: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}

// Controllo se l'utente ha già finanziato il progetto oggi
try {
    $in = ['p_email' => $_SESSION['email'], 'p_nome_progetto' => $progetto['nome']];
    $result = sp_invoke('sp_util_utente_finanziato_progetto_oggi', $in);
    $finanziatoOggi = $result[0]['finanziato_oggi'];
} catch (PDOException $ex) {
    redirect(
        false,
        "Errore durante il controllo del finanziamento odierno: " . $ex->errorInfo[2],
        '../public/progetti.php'
    );
}
?>

<?php require '../components/header.php'; ?>
    <div class="container my-4">
        <!-- Messaggio di successo/errore post-azione -->
        <?php include '../components/error_alert.php'; ?>
        <?php include '../components/success_alert.php'; ?>

        <!-- Progetto Section -->
        <div class="card mb-4 shadow-sm">
            <!-- Header: Nome Progetto, Tipo e Stato -->
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

            <!-- Body: Dettagli Principali -->
            <div class="card-body">
                <p class="fs-5">
                    <strong>Creatore:</strong> <?php echo htmlspecialchars($progetto['email_creatore']); ?>
                    (Affidabilità: <?php echo htmlspecialchars($affidabilita); ?>%)
                </p>
                <hr>
                <div class="card mb-3">
                    <div class="card-header d-inline-flex align-items-center justify-content-between">
                        <p class="fw-bold fs-5">Descrizione</p>
                        <?php if (isProgettoOwner($_SESSION['email'], $progetto['nome'])): ?>
                            <form action="../public/progetto_aggiorna.php?attr=descrizione&nome=<?php echo htmlspecialchars($progetto['nome']); ?>"
                                  method="post">
                                <button type="submit" class="btn btn-warning">Modifica</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <!-- Descrizione e Foto del progetto -->
                    <div class="card-body">
                        <?php if (!empty($progetto['descrizione'])): ?>
                            <p><?php echo htmlspecialchars($progetto['descrizione']); ?></p>
                        <?php else: ?>
                            <p>Nessuna descrizione disponibile per questo progetto.</p>
                        <?php endif; ?>
                        <hr>
                        <?php if (!empty($photos)): ?>
                            <p class="text-muted small"><?php if (count($photos) > 4): ?>(Scorri per visualizzare le restanti)<?php endif; ?></p>
                            <div class="card-body">
                                <div class="d-flex flex-nowrap overflow-auto">
                                    <?php foreach ($photos as $photo): ?>
                                        <div class="flex-shrink-0 w-25 p-2">
                                            <?php $base64 = base64_encode($photo['foto']); ?>
                                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>"
                                                 class="img-fluid rounded"
                                                 alt="Foto progetto">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-body">
                                <p>Nessuna foto disponibile per questo progetto.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer: Data Inserimento, Scadenza e Giorni Rimasti -->
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

        <!-- Finanziamenti / Budget Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header fs-5 d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <strong>Finanziamenti</strong>
                    <small class="text-muted fs-6">
                        Finanzia il progetto per aiutare il creatore a raggiungere il budget richiesto.
                        Ogni finanziamento è ricompensato con una delle reward disponibili.
                    </small>
                </div>
                <?php if (isProgettoOwner($_SESSION['email'], $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                    <form action="../public/progetto_aggiorna.php?attr=budget&nome=<?php echo htmlspecialchars($progetto['nome']); ?>"
                          method="post">
                        <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Body: Budget, Totale Finanziamenti, e Percentuale di Completamento -->
            <div class="card-body">
                <!-- Budget -->
                <div class="bg-secondary-subtle p-1 rounded text-center">
                    <p class="fs-4">
                        <strong>Budget:</strong> <?php echo htmlspecialchars(number_format($progetto['budget'], 2)); ?>€
                    </p>
                </div>
                <hr>
                <!-- Percentuale di completamento -->
                <div class="d-flex w-100 fw-bold justify-content-center fs-5">
                    <?php echo round($progetto['percentuale'], 2); ?>%
                </div>

                <!-- Barra di progresso Finanziamenti / Budget -->
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

                <!-- Reward Section -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="fs-5 d-flex flex-column">
                            <strong>Reward</strong>
                            <small class="text-muted fs-6">
                                <?php if (count($rewards) > 4): ?>(Scorri per visualizzare le restanti)<?php endif; ?>
                                Visualizza le reward disponibili per il progetto. Ogni reward è ottenibile con un
                                finanziamento di un certo importo.
                            </small>
                        </div>
                        <?php if (isProgettoOwner($_SESSION['email'], $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                            <form action="../public/progetto_aggiorna.php?attr=reward&nome=<?php echo htmlspecialchars($progetto['nome']); ?>"
                              method="post">
                                <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Body: Lista delle Reward -->
                    <div class="card-body">
                        <div class="d-flex flex-nowrap overflow-auto">
                            <?php if (!empty($rewards)): ?>
                                <?php foreach ($rewards as $reward): ?>
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
                                                <!-- Foto della reward -->
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
                            <?php else: ?>
                                <p>Nessuna reward disponibile per questo progetto.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer: Finanzia Progetto -->
            <?php if ($progetto['stato'] === 'aperto'): ?>
                <?php if (!$finanziatoOggi): ?>
                    <div class="card-footer">
                        <form action="../public/finanziamento_conferma.php" method="post">
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

        <!-- Profili / Componenti Section -->
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
                            <?php if (count($componenti) > 4): ?>(Scorri per visualizzare i restanti)<?php endif; ?>
                            Di seguito i componenti richiesti per il progetto hardware.
                        </small>
                    <?php endif; ?>
                </div>
                <?php if (isProgettoOwner($_SESSION['email'], $progetto['nome']) && $progetto['stato'] === 'aperto'): ?>
                    <?php
                    $tipo = ($progetto['tipo'] === 'SOFTWARE') ? 'profili': 'componenti';
                    $nome_progetto = htmlspecialchars($progetto['nome']);
                    ?>
                    <form action="../public/progetto_aggiorna.php?attr=<?php echo $tipo ?>&nome=<?php echo $nome_progetto; ?>"
                          method="post">
                        <button type="submit" class="btn btn-warning mt-2">Modifica</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-nowrap overflow-auto">
                <?php if ($progetto['tipo'] === 'SOFTWARE'): ?>
                    <?php if ($progetto['stato'] === 'aperto'): ?>
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
                                    <div class="card-footer">
                                        <?php if (!isProgettoOwner($_SESSION['email'], $progetto['nome'])): ?>
                                        <!-- Candidatura Button -->
                                            <?php
                                            // Controllo se l'utente ha già inviato una candidatura
                                            $userHasApplied = false;
                                            $userWasRejected = false;

                                            // Recupero lo stato della candidatura
                                            try {
                                                $in = [
                                                    'p_email_utente' => $_SESSION['email'],
                                                    'p_nome_progetto' => $progetto['nome'],
                                                    'p_nome_profilo' => $nome_profilo
                                                ];
                                                $result = sp_invoke('sp_partecipante_getStatus', $in);

                                                if (!empty($result)) {
                                                    $status = $result[0]['stato'] ?? '';
                                                    if ($status === 'potenziale') {
                                                        $userHasApplied = true;
                                                    } elseif ($status === 'rifiutato') {
                                                        $userWasRejected = true;
                                                    }
                                                }

                                                // Controllo se l'utente è idoneo per la candidatura
                                                if (!$userHasApplied && !$userWasRejected) {
                                                    $eligibilityResult = sp_invoke('sp_util_partecipante_is_eligible', $in);
                                                    $userIsEligible = $eligibilityResult[0]['eligible'] ?? false;
                                                }
                                            } catch (PDOException $ex) {
                                                print($ex->errorInfo[2]);
                                            }
                                            ?>
                                        <?php elseif (isset($acceptedParticipants[$nome_profilo])): ?>
                                            <!-- Profilo occupato -->
                                            <button class="btn btn-secondary w-100" disabled>
                                                Occupato da <?php echo htmlspecialchars($acceptedParticipants[$nome_profilo]['nickname']); ?>
                                            </button>
                                        <?php elseif ($userHasApplied): ?>
                                            <!-- Utenza ha già inviato una candidatura -->
                                            <button class="btn btn-warning w-100" disabled>Candidatura in attesa</button>
                                        <?php elseif ($userWasRejected): ?>
                                            <!-- Utente è stato rifiutato -->
                                            <button class="btn btn-danger w-100" disabled>Candidatura rifiutata</button>
                                        <?php elseif (!$userIsEligible): ?>
                                            <!-- Utente non è idoneo per la candidatura -->
                                            <button class="btn btn-secondary w-100" disabled>Non idoneo</button>
                                        <?php else: ?>
                                            <!-- Utente può inviare una candidatura -->
                                            <form action="../actions/candidatura_insert.php" method="post">
                                                <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                                <input type="hidden" name="nome_profilo" value="<?php echo htmlspecialchars($nome_profilo); ?>">
                                                <button type="submit" class="btn btn-primary w-100">Candidati</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Il progetto è chiuso alle candidature.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!empty($componenti)): ?>
                        <?php foreach ($componenti as $componente): ?>
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
                    <?php else: ?>
                        <p>Componenti non disponibili per questo progetto.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <!-- Commenti Section -->
        <div class="card mt-4">
            <div class="card-header fs-5 d-flex flex-column">
                <strong>Commenti</strong>
                <small class="text-muted fs-6">
                    Lascia un commento per esprimere la tua opinione sul progetto.
                </small>
            </div>
            <div class="card-body overflow-auto" style="max-height: 500px;">
                <?php if (!empty($commenti)): ?>
                    <?php foreach ($commenti as $commento): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>
                                    <?php echo htmlspecialchars($commento['nickname']); ?>
                                    <?php if ($commento['email_utente'] === $progetto['email_creatore']): ?>
                                        (Creatore)
                                    <?php endif; ?>
                                    <?php if ($commento['email_utente'] === $_SESSION['email']): ?>
                                        (You)
                                    <?php endif; ?>
                                </strong>
                                <!-- Mostra la data del commento -->
                                <small class="text-muted mx-2"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($commento['data']))); ?></small>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($commento['testo']); ?></p>
                            </div>
                            <!-- Mostra il bottone per eliminare il commento -->
                            <?php if ($commento['email_utente'] === $_SESSION['email'] || $_SESSION['is_admin']): ?>
                                <div class="card-footer">
                                    <form action="../actions/commento_delete.php" method="post">
                                        <input type="hidden" name="id_commento" value="<?php echo htmlspecialchars($commento['id']); ?>">
                                        <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                        <input type="hidden" name="email_utente" value="<?php echo htmlspecialchars($commento['email_utente']); ?>">
                                        <button type="submit" class="btn btn-danger">Elimina</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            <!-- Mostra la risposta al commento -->
                            <?php if (!empty($commento['risposta'])): ?>
                                <div class="card-footer">
                                    <strong>
                                        Risposta
                                        (<?php if (isProgettoOwner($_SESSION['email'], $progetto['nome'])): ?>You<?php else: ?>Creatore<?php endif; ?>)
                                    </strong>
                                    <p><?php echo htmlspecialchars($commento['risposta']); ?></p>
                                    <?php if (isProgettoOwner($_SESSION['email'], $progetto['nome']) || $_SESSION['is_admin']): ?>
                                        <form action="../actions/commento_risposta_delete.php" method="post">
                                            <input type="hidden" name="id_commento" value="<?php echo htmlspecialchars($commento['id']); ?>">
                                            <input type="hidden" name="nome_progetto" value="<?php echo htmlspecialchars($progetto['nome']); ?>">
                                            <button type="submit" class="btn btn-danger">Elimina</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php elseif (isProgettoOwner($_SESSION['email'], $progetto['nome'])): ?>
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
                <?php else: ?>
                    <p>Nessun commento disponibile per questo progetto.</p>
                <?php endif; ?>
            </div>

            <!-- Form per inserire un commento -->
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