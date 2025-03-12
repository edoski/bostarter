# TODO
---
- [ ] check for any leftover TODO's in project
- [ ] connect mongodb to php
- [ ] env vars for mysql and php?
- [ ] PASTE FULL SQL INIT FILE TO [[#**7.1. Inizializzazione DB**]]
- [ ] review entire report and ensure consistent tables, attributes etc
	- [ ] update LISTA DELLE OPERAZIONI MAYBE!!!!!
- [ ] create README.md for repo to explain how to setup and run project
- [ ] hmmm maybe docker cool but idk
- [ ] redo screenshots of funzionalità section once website complete
- [ ] export to pdf using pandoc
	- [ ] potentially need to fix mismatching links text for correct rendering
	- [ ] ensure clickable links for ToC

# DOMANDE PER PROF (READ TRACCIA BEFORE)
- **Per quanto riguarda la registrazione di utenti sulla piattaforma:** È ammesso che **un utente possa registrarsi come admin/amministratore**, anche se è un ruolo privilegiato?
- **Per quanto riguardano i finanziamenti:** 
	1. Si presuppone che il progetto accetti finanziamenti fatti da un utente **solo una volta al giorno?** Questo mio dubbio nasce dalla definizione di data per un finanziamento: Per "data" intende **in MySQL l'equivalente di "DATE" oppure "DATETIME"?** Perché con **DATE** un utente può effettuare finanziamenti per un progetto **una volta al giorno**, mentre con **DATETIME una volta per minuto**.
	2. È ammesso che **un utente possa finanziare il progetto con un importo che ecceda il budget totale / rimanente**? Nella mia implementazione attuale, tale scenario è ammesso e quello che accade è: Lo stato del progetto viene posto a "chiuso" (in tal modo da non accettare ulteriori finanziamenti), e la somma dei finanziamenti **+ l'eccesso da quest'ultimo finanziamento** è chiaramente visibile quando si visualizza il progetto.
	3. Nella traccia del progetto viene specificato che la scelta della reward va fatta "a valle del finanziamento di un progetto". Per "a valle", intende da un punto di vista del database che prima venga inserito nella tabella dei finanziamenti l'importo e dati dell'utente e finanziatore, **ed altrove viene associata la reward al finanziamento**, oppure è accettabile **mantenere il riferimento della reward** (il suo codice) direttamente **all'interno della tabella dei finanziamenti**?
- **Per quanto riguardano le candidature ai profili di un progetto software:** Si presuppone che per un dato profilo di un progetto, se il creatore accetta un utente candidato come partecipante al progetto, **il profilo in questione viene chiuso (non ammette candidature), oppure può accettare ulteriori candidature da altri utenti?** Ovvero, Il "Profilo" è un'entità astratta che non ha limiti di istanza, oppure è concreta e con limite di singola istanza?
- **Per quanto riguardano i profili/componenti per progetti software/hardware:** È ammissibile l'esistenza di un progetto software/hardware che non dispone di profili/componenti (ma che comunque possono essere inseriti in secondo luogo dal creatore)?



## use checkProgettoOwner() in all CHECKS in place of existing check


## progetto dettagli if profilo has no skill (ex. just created it) render text in card body saying "Nessuna skill associata."
- consider if in db should rework profilo to have boolean is_aperto attribute which makes it so that only those w it being true get returned as available/visible profiles in platform. profilo is_aperto default false and becomes set to true only when user in progetto_aggiorna_profili clicks like Salva Profilo or something, until then it remains invisible to all except him


### to make all php files slimmer consider abstracting reused visual components and requiring them in file, and same thing for data gathering if there are identical try-catch sp_invoke data initialisers like $progetto or $finanziamenti
- if u do this evaluate if need to refactor progetto_aggiorna to use $\_POST\['nome] instead of $\_GET\['nome] and standardise all data gathering ways and whatever else/files for consistency

## TODO SP
- sp_progetto_insert: CREATORE
- sp_reward_insert: CREATORE

- sp_componente_insert: CREATORE
- sp_componente_delete: CREATORE
- sp_componente_update: CREATORE

- sp_profilo_insert: CREATORE
- sp_profilo_delete: CREATORE
- sp_skill_profilo_insert: CREATORE
- sp_skill_profilo_delete: CREATORE
- sp_skill_profilo_update: CREATORE

## REMEMBER FOR HW PROJECTS 
- WHEN USER ADDS COMPONENTS PRICE/QUANTITY HE MUST BE ALERTED IF THEIR SUM EXCEEDS THE PROJECT BUDGET AND TO CONFIRM ADD THEM OR NOT → MAKE SURE THE BUDGET RECALCULATION TRIGGERS ARE CORRECT

## for mongodb 
- add a error collection which just logs any sql signal state 45000 errors triggered, this type of log occurs in the fail-early initial checks at top of most php files, and in all PDOException try-catches
- almost anywhere an sp_invoke() in php is called, thats where i must trigger a log as it indicates an action taking place

## keep track of website structure, when complete paste it atop of section 6.2 report
```
bostarter/
├── actions/
│   ├── candidatura_insert.php
│   ├── candidatura_update.php
│   ├── commento_delete.php
│   ├── commento_insert.php
│   ├── commento_risposta_delete.php
│   ├── commento_risposta_insert.php
│   ├── finanziamento_insert.php
│   ├── foto_delete.php
│   ├── login_handler.php
│   ├── logout.php
│   ├── progetto_budget_update.php
│   ├── progetto_descrizione_update.php
│   ├── register_handler.php
│   ├── reward_insert.php
│   ├── skill_curriculum_delete.php
│   ├── skill_curriculum_insert.php
│   ├── skill_curriculum_update.php
│   ├── skill_insert.php
│   ├── skill_update.php
│   └── utente_convert_creatore.php
├── components/
│   ├── error_alert.php
│   ├── footer.php
│   ├── header.php
│   └── success_alert.php
├── config/
│   └── config.php
├── db/
│   ├── bostarter_demo.sql
│   └── bostarter_init.sql
├── docs/
│   └── DB-PRJ-REPORT.md
├── functions/
│   ├── checks.php
│   ├── redirect.php
│   └── sp_invoke.php
├── public/
│   ├── libs/
│   │   ├── bootstrap.bundle.min.js
│   │   └── bootstrap.min.css
│   ├── candidature.php
│   ├── curriculum.php
│   ├── finanziamenti.php
│   ├── finanziamento_conferma.php
│   ├── home.php
│   ├── index.php
│   ├── login.php
│   ├── profilo_dettagli.php
│   ├── progetti.php
│   ├── progetto_aggiorna.php
│   ├── progetto_crea.php
│   ├── progetto_dettagli.php
│   ├── register.php
│   └── statistiche.php
```

# INDICE
---
### **1.  [[DB-PRJ-REPORT#1. ANALISI DEI REQUISITI|Analisi dei Requisiti]]**
- **1.1. [[DB-PRJ-REPORT#1.1. DECOMPOSIZIONE DEL TESTO**|Decomposizione del Testo]]**
- **1.2. [[DB-PRJ-REPORT#1.2. LISTA DELLE OPERAZIONI**|Lista delle Operazioni]]**
- **1.3. [[#1.3. GLOSSARIO DEI DATI**|Glossario dei Dati]]**
### **2. [[#2. PROGETTAZIONE CONCETTUALE|Progettazione Concettuale]]**
- **2.1. [[#2.1. DIAGRAMMA E-R**|Diagramma E-R]]**
- **2.2. [[#2.2. DIZIONARIO DELLE ENTITÀ**|Dizionario delle Entità]]**
- **2.3. [[#2.3. DIZIONARIO DELLE RELAZIONI**|Dizionario delle Relazioni]]**
- **2.4. [[#2.4. BUSINESS RULES**|Business Rules]]**
### **3. [[#3. PROGETTAZIONE LOGICA|Progettazione Logica]]**
- **3.1. [[#3.1. ANALISI DELLE RIDONDANZE**|Analisi delle Ridondanze]]**
- **3.2. [[#3.2. LISTA DELLE TABELLE**|Lista delle Tabelle]]**
- **3.3. [[#3.3. LISTA DEI VINCOLI INTER-RELAZIONALI**|Lista dei Vincoli Inter-relazionali]]**
### **4. [[#4. NORMALIZZAZIONE|Normalizzazione]]**
- **4.1. [[#4.1. ANALISI|Analisi (3FN e FNBC)]]**
### **5. [[#5. RIFLESSIONI|Riflessioni]]**
...
### **6. [[#6. FUNZIONALITÀ|Funzionalità]]**
- **6.1. [[#**6.1. BACKEND (MySQL)**|BACKEND (MySQL)]]**
- **6.2. [[#**6.2. FRONTEND (PHP)**|FRONTEND (PHP)]]**
- **6.3. [[#**6.3. LOGGING (MongoDB)**|LOGGING (MongoDB)]]**
### **7. [[#7. APPENDICE|Appendice]]**
- **7.1. [[#7.1. Inizializzazione DB|Inizializzazione DB]]**
- **7.2. [[#7.2. Popolamento DB|Popolamento DB]]**
- **7.3. [[#7.3. Script|Script]]**

# **1. ANALISI DEI REQUISITI**
---
## **1.1. DECOMPOSIZIONE DEL TESTO**

#### `UTENTE`
- Tutti gli utenti della piattaforma dispongono di: **email** (univoca), **nickname**, **password**, **nome**, **cognome**, **anno di nascita**, e un **luogo di nascita**. Inoltre, ogni utente può indicare le proprie skill di curriculum
- Gli utenti **possono appartenere** (non necessariamente) a due sotto-categorie: **amministratori** e **creatori**
- **Ogni utente** della piattaforma può **finanziare un progetto**
- Un utente **può candidarsi ad un numero qualsiasi di profili**

#### `SKILL DI CURRICULUM`
- Le skill di curriculum consistono in una sequenza di: **<competenza, livello>**, dove la **competenza è una stringa** ed il **livello è un numero tra 0 e 5** (es. <AI, 3>)
- La lista delle competenze è **comune a tutti gli utenti** della piattaforma

#### `AMMINISTRATORE`
- Gli utenti amministratori dispongono anche di **un codice di sicurezza**
- **Solo** gli utenti **amministratori** possono **popolare la lista delle competenze**

#### `CREATORE`
- Gli utenti creatori dispongono anche dei campi: **nr_progetti** ed **affidabilità**
- **Solo** un utente **creatore** può **inserire uno o più progetti**
- L’utente creatore può eventualmente **inserire una risposta per ogni singolo commento** (un commento ha al **massimo 1 risposta**)
- L’utente creatore può **accettare o meno la candidatura** di un potenziale partecipante del **proprio progetto software**

#### `PROGETTO GENERICO`
- Ogni progetto dispone di: un **nome** (univoco), un **campo descrizione**, una **data di inserimento**, **una o più foto**, un **budget** da raggiungere per avviare il progetto, una **data limite** entro cui raggiungere il budget, uno **stato**. Lo stato è un campo di tipo **enum (aperto/chiuso)**
- Ogni progetto è **associato ad un solo utente creatore**
- Ogni progetto prevede una **lista di reward**
- Ogni progetto appartiene esclusivamente ad una di due categorie: progetti **hardware** o progetti **software**
- Nel momento in cui la **somma totale degli importi dei finanziamenti supera il budget** del progetto, oppure il **progetto resta in stato aperto oltre la data limite**, lo stato di tale progetto **diventa pari a chiuso**
- Un **progetto chiuso** non accetta **ulteriori finanziamenti**

#### `REWARD`
- Una reward dispone di: un **codice** (univoco), una **breve descrizione**, una **foto**

#### `PROGETTO HARDWARE`
- Nel caso dei progetti hardware, è presente anche la **lista delle componenti necessarie**

#### `COMPONENTE HARDWARE`
- Ogni componente ha: un **nome** (univoco), una **descrizione**, un **prezzo**, una **quantità (>0)**

#### `PROGETTO SOFTWARE`
- Nel caso dei progetti software, viene elencata la **lista dei profili necessari** per lo sviluppo
- Un progetto software **può ricevere un numero qualsiasi di candidature per un certo profilo**

#### `PROFILO`
- Ogni profilo dispone di: un **nome** (es. “Esperto AI”) e di **skill richieste**

#### `SKILL DI PROFILO`
- Le **skill di profilo** consistono in una sequenza **<competenza, livello>**, dove la **competenza** è una **stringa** (tra quelle **presenti in piattaforma**) ed il **livello** è un **numero tra 0 e 5**

#### `FINANZIAMENTO`
- Ogni finanziamento dispone di: un **importo** ed una **data**.
- Un utente potrebbe inserire **più finanziamenti per lo stesso progetto**, ma in **date diverse**
- Ad ogni finanziamento è associata **una sola reward**, tra quelle **previste per il progetto** finanziato

#### `COMMENTO`
- Un utente può **inserire commenti relativi ad un progetto** Ogni commento dispone di: un **id** (univoco), una **data** ed un campo **testo**

#### `PARTECIPANTE`
- È prevista la possibilità per gli **utenti** di **candidarsi come partecipanti allo sviluppo di un progetto software**
- La piattaforma consente ad un utente di inserire una candidatura su un profilo **SOLO se, per ogni skill richiesta da un profilo, l’utente dispone di un livello superiore o uguale** al valore richiesto

#### `LOG` (MongoDB)
- Si vuole tenere traccia di **tutti gli eventi che occorrono nella piattaforma**, relativamente **all’inserimento di nuovi dati** (es. nuovi utenti, nuovi progetti, etc)
- Tali eventi vanno inseriti, sotto forma di **messaggi di testo**, **all’interno di un log**, implementato in un’ apposita **collezione MongoDB**

## **1.2. LISTA DELLE OPERAZIONI**

#### Operazioni che riguardano TUTTI gli utenti:
- Autenticazione/registrazione sulla piattaforma
- Inserimento delle proprie skill di curriculum
- Visualizzazione dei progetti disponibili
- Finanziamento di un progetto (aperto). Un utente può finanziare anche il progetto di cui è creatore
- Scelta della reward a valle del finanziamento di un progetto
- Inserimento di un commento relativo ad un progetto
- Inserimento di una candidatura per un profilo richiesto per la realizzazione di un progetto software

#### Operazioni che riguardano SOLO gli amministratori:
- Inserimento di una nuova stringa nella lista delle competenze
- In fase di autenticazione, oltre a username e password, viene richiesto anche il codice di sicurezza
- Cancellazione di commenti

#### Operazioni che riguardano SOLO i creatori:
- Inserimento di un nuovo progetto
- Inserimento delle reward per un progetto
- Inserimento/cancellazione di una risposta ad un commento
- Inserimento/cancellazione/aggiornamento di un profilo per un progetto software
- Accettazione o meno di una candidatura
- Inserimento/cancellazione/aggiornamento di componenti per un progetto hardware

## **1.3. GLOSSARIO DEI DATI**

| **TERMINE**             | **DESCRIZIONE**                                                                                                                                                                                                                                     | **SINONIMI** | **COLLEGAMENTI**                                                                      |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------ | ------------------------------------------------------------------------------------- |
| **UTENTE**              | Un utente generico della piattaforma BOSTARTER. Generalizzazione (non totale) di AMMINISTRATORE/CREATORE.                                                                                                                                           |              | AMMINISTRATORE / CREATORE, SKILL DI CURRICULUM, FINANZIAMENTO, PARTECIPANTE, COMMENTO |
| **SKILL DI CURRICULUM** | Le competenze specifiche di ogni utente. OGNI utente dispone di esse.                                                                                                                                                                               |              | UTENTE                                                                                |
| **AMMINISTRATORE**      | Un utente privilegiato della piattaforma, può modificare dati sensitivi come la lista delle competenze. Essendo una specializzazione di UTENTE, è associato in via indiretta ad ogni collegamento di quest'ultimo.                                  |              | UTENTE                                                                                |
| **CREATORE**            | Un utente che ha il permesso di aprire e gestire progetti (propri) sulla piattaforma.  Essendo una specializzazione di UTENTE, è associato in via indiretta ad ogni collegamento di quest'ultimo.                                                   |              | UTENTE, PROGETTO GENERICO /  HARDWARE /   SOFTWARE                                    |
| **PROGETTO GENERICO**   | Un progetto generico sulla piattaforma; si tratta di una generalizzazione (totale) di PROGETTO HARDWARE/SOFTWARE. Viene gestito da un solo utente creatore.                                                                                         | Progetto     | PROGETTO HARDWARE / SOFTWARE, CREATORE, REWARD, FINANZIAMENTO, COMMENTO               |
| **REWARD**              | Un premio associato ad un progetto, viene offerto ad utenti che finanziano il progetto.                                                                                                                                                             | Premio       | PROGETTO GENERICO, FINANZIAMENTO                                                      |
| **PROGETTO HARDWARE**   | Un progetto specifico all'ambito hardware, dispone di una lista di componenti necessarie. Specializzazione di PROGETTO GENERICO.                                                                                                                    |              | PROGETTO GENERICO, CREATORE, COMPONENTE HARDWARE                                      |
| **PROGETTO SOFTWARE**   | Un progetto specifico nell'ambito software, dispone di una lista di profili richiesti per lo sviluppo, e può avere più partecipanti. Specializzazione di PROGETTO GENERICO.                                                                         |              | PROGETTO GENERICO, CREATORE, PARTECIPANTE, PROFILO                                    |
| **PROFILO**             | Un profilo particolare richiesto per un progetto software, dispone di un nome ed una lista di skill richieste. Potenziali partecipanti per un progetto software si misurano sulla base del profilo e livello delle skill sottostanti richieste.     |              | PROGETTO SOFTWARE, SKILL DI PROFILO                                                   |
| **SKILL DI PROFILO**    | Una competenza specifica appartenente ad un profilo per progetti software.                                                                                                                                                                          |              | PROFILO                                                                               |
| **FINANZIAMENTO**       | Un finanziamento economico fatto da un qualunque tipo di utente per un progetto hardware/software.                                                                                                                                                  |              | UTENTE, PROGETTO GENERICO, REWARD                                                     |
| **COMMENTO**            | Un commento fatto da un qualunque tipo di utente per un progetto hardware/software. Può contenere al massimo una risposta da parte dell'utente creatore.                                                                                            |              | UTENTE, PROGETTO GENERICO                                                             |
| **PARTECIPANTE**        | Un potenziale o effettivo partecipante ad un progetto software. Qualunque utente che non è creatore del progetto software può candidarsi ad esso se dispone del profilo e livelli necessari, e può essere accettato/rifiutato dall'utente creatore. | Candidato    | UTENTE, PROGETTO SOFTWARE                                                             |

# **2. PROGETTAZIONE CONCETTUALE**
---
## **2.1. DIAGRAMMA E-R**
![[DB-PRJ-ERD.png]]

## **2.2. DIZIONARIO DELLE ENTITÀ**

| **ENTITÀ**            | **DESCRIZIONE**                                                                                                                            | **ATTRIBUTI**                                                                   | **IDENTIFICATORE**                |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------- | --------------------------------- |
| **UTENTE**            | Utente generico della piattaforma BOSTARTER. Ogni utente (admin/creatore) viene inglobato in questa entità.                                | email, password, nickname, nome, cognome, anno_nascita, luogo_nascita           | email                             |
| **ADMIN**             | Specializzazione di UTENTE. Vengono inseriti gli utenti privilegiati/amministratori.                                                       | email_utente, codice_sicurezza                                                  | email_utente                      |
| **CREATORE**          | Specializzazione di UTENTE. Vengono inseriti gli utenti che creano e gestiscono progetti.                                                  | email_utente, nr_progetti, affidabilità                                         | email_utente                      |
| **PROGETTO**          | Un progetto generico della piattaforma. Ogni progetto (software/hardware) viene inglobato in questa entità.                                | nome, email_creatore, descrizione, budget, stato, data_inserimento, data_limite | nome                              |
| **FOTO**              | Una o più foto associate ad un determinato progetto.                                                                                       | id, nome_progetto, foto                                                         | id, nome_progetto                 |
| **REWARD**            | Una o più reward associate ad un determinato progetto.                                                                                     | codice, nome_progetto, descrizione, foto, min_importo                           | codice, nome_progetto             |
| **COMMENTO**          | Uno o più commenti associati ad un utente ed un progetto. Opzionalmente contengono una risposta dall'utente creatore.                      | id, email_utente, nome_progetto, data, testo, risposta                          | id                                |
| **PROGETTO_SOFTWARE** | Specializzazione di PROGETTO. Vengono inseriti i progetti di tipo software.                                                                | nome_progetto                                                                   | nome_progetto                     |
| **PROGETTO_HARDWARE** | Specializzazione di PROGETTO. Vengono inseriti i progetti di tipo hardware.                                                                | nome_progetto                                                                   | nome_progetto                     |
| **COMPONENTE**        | Una o più componenti fisiche necessarie per un progetto hardware.                                                                          | nome_componente, nome_progetto, descrizione, quantità, prezzo                   | nome_componente, nome_progetto    |
| **PROFILO**           | Uno o più profili necessari per un progetto software.                                                                                      | nome_profilo                                                                    | nome_profilo                      |
| **SKILL**             | Una o più competenze richieste/disponibili sulla piattaforma per progetti software. La lista di competenze è gestita dagli amministratori. | competenza                                                                      | competenza                        |
| **FINANZIAMENTO**     | Un finanziamento economico fatto da un utente, verso un progetto, associato ad una reward.                                                 | data, email_utente, nome_progetto, codice_reward, importo                       | data, email_utente, nome_progetto |

## **2.3. DIZIONARIO DELLE RELAZIONI**

| **RELAZIONI**              | **DESCRIZIONE**                                                                                                                                                            | **COMPONENTI**                | **ATTRIBUTI**     |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------- | ----------------- |
| **SKILL_CURRICULUM**       | Le competenze effettive di ciascun utente, tratte da SKILL ed associate al livello dell'utente.                                                                            | UTENTE, SKILL                 | livello_effettivo |
| **UTENTE_FINANZIAMENTO**   | L'associazione di un finanziamento all'utente che l'ha fatto.                                                                                                              | UTENTE, FINANZIAMENTO         |                   |
| **UTENTE_COMMENTO**        | L'associazione di un commento all'utente che l'ha postato.                                                                                                                 | UTENTE, COMMENTO              |                   |
| **PARTECIPANTE**           | Un utente e  potenziale candidato per partecipare ad un progetto software esistente. Candidati che non possiedono le competenze/livelli necessari non vengono considerati. | UTENTE, PROGETTO_SOFTWARE     | stato             |
| **FINANZIAMENTO_PROGETTO** | L'associazione di un finanziamento ad un progetto.                                                                                                                         | FINANZIAMENTO, PROGETTO       |                   |
| **COMMENTO_PROGETTO**      | L'associazione di un commento ad un progetto.                                                                                                                              | COMMENTO, PROGETTO            |                   |
| **REWARD_PROGETTO**        | L'associazione di una reward ad un progetto.                                                                                                                               | REWARD, PROGETTO              |                   |
| **CREATORE_PROGETTO**      | L'associazione di un utente creatore al progetto che ha creato.                                                                                                            | CREATORE, PROGETTO            |                   |
| **FOTO_PROGETTO**          | La lista di una o più foto che rappresentano un progetto.                                                                                                                  | FOTO, PROGETTO                |                   |
| **FINANZIAMENTO_REWARD**   | L'associazione di una reward per un finanziamento di un progetto.                                                                                                          | FINANZIAMENTO, REWARD         |                   |
| **COMPONENTE_PROGETTO**    | La lista di una o più componenti fisiche necessarie per un progetto hardware.                                                                                              | PROGETTO_HARDWARE, COMPONENTE |                   |
| **PROFILO_PROGETTO**       | La lista di uno o più profili necessari per lo sviluppo di un progetto software.                                                                                           | PROGETTO_SOFTWARE, PROFILO    |                   |
| **SKILL_PROFILO**          | La lista di una o più skill comprese all'interno di un profilo di sviluppo, e il livello richiesto per ciascuna di esse.                                                   | SKILL, PROFILO                | livello_richiesto |

**N.B.** **PARTECIPANTE**.stato → enum: {"accettato", "rifiutato", "potenziale"}

## **2.4. BUSINESS RULES**

|         | **REGOLE DI VINCOLO**                                                                                                                                                                                     |
| ------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **1.**  | Per le **skill di curriculum**, il **livello è un numero tra 0 e 5**                                                                                                                                      |
| **2.**  | Per le **skill di profilo**, il **livello è un numero tra 0 e 5**                                                                                                                                         |
| **3.**  | **Ogni componente** in un progetto **hardware** dispone di una **quantità maggiore di zero**                                                                                                              |
| **4.**  | **Solo** gli utenti **amministratori** possono **popolare la lista delle competenze**                                                                                                                     |
| **5.**  | Se la **somma totale degli importi dei finanziamenti supera il budget** del progetto, lo stato di tale progetto **diventa pari a chiuso**                                                                 |
| **6.**  | Se il **progetto resta in stato aperto oltre la data limite**, lo stato di tale progetto **diventa pari a chiuso**                                                                                        |
| **7.**  | Un **progetto chiuso** non accetta **ulteriori finanziamenti**                                                                                                                                            |
| **8.**  | Ogni commento ha al **massimo 1 risposta** scritta dall'**utente creatore del progetto**                                                                                                                  |
| **9.**  | La piattaforma consente ad un utente di inserire una candidatura su un profilo **SOLO se, per ogni skill richiesta da un profilo, l’utente dispone di un livello superiore o uguale** al valore richiesto |
| **10.** | La **reward** ottenuta da un **finanziamento** dipende dal suo **importo** (confronto dinamico fra FINANZIAMENTO.importo e REWARD.min_importo)                                                            |

# **3. PROGETTAZIONE LOGICA**
---
## **3.1. ANALISI DELLE RIDONDANZE**

Si vuole valutare se la seguente ridondanza: **campo `nr_progetti`** relativo ad un utente creatore debba essere **tenuta o eliminata**, sulla base delle seguenti operazioni:

#### **Operazioni**
- $Op_{1}$: **Aggiungere (write)** un nuovo progetto ad un utente creatore esistente **(1 volte/mese, interattiva)**
- $Op_{2}$: **Visualizzare (read)** tutti i progetti e tutti i finanziamenti **(1 volta/mese, batch)**
- $Op_{3}$: **Contare (read)** il numero di progetti associati ad uno specifico utente **(3 volte/mese, batch)**

#### **Contesto**
Si deriva il **costo $c$ di un'operazione $O_{T}$**, dunque $c(O_{T})$, utilizzando la seguente formula:
$$\begin{align*}
c(O_{T})=f(O_{T})\cdot w_{T}\cdot (\alpha \cdot NC_{\text{write}}+NC_{\text{read}})
\end{align*}$$
Dove:
- $f(O_{T})=$ **Frequenza** dell'operazione
- $NC_{\text{write}}=$ Numero di **accessi in scrittura** a componenti (entità/relazioni) dello schema
- $NC_{\text{read}}=$ Numero di **accessi in lettura** a componenti (entità/relazioni) dello schema
- $w_{T}=$ **Peso** dell’operazione (interattiva/batch)
- $\alpha=$ **Coefficiente moltiplicativo** delle operazioni in **scrittura**

#### **Coefficienti per l'Analisi**
- $\alpha=2$
- $w_{I}=1$
- $w_{B}=0.5$

#### **Tabella dei Volumi**
- 10 progetti
- 3 finanziamenti per progetto
- 5 utenti
- 2 progetti per utente

### **Operazione 1**
---
$Op_{1}$: **Aggiungere (write)** un nuovo progetto ad un utente creatore esistente **(1 volte/mese, interattiva)**.

#### Includendo `nr_progetti`

**Logica**
8. Incrementa di uno il numero di progetti gestiti dall'utente creatore
9. Crea un nuovo progetto
	- Una entry per il progetto
	- Una o più entry per le foto del progetto
	- Una o più entry per le reward del progetto
10. Inserisci una entry nella tabella relativa al tipo di progetto (software/hardware)
	- IF software → Una o più entry per i profili richiesti
	- IF hardware → Una o più entry per le componenti richieste

**Procedura** (assumendo una foto, una reward, e un profilo/componente)
- 1 `UPDATE` in `CREATORE.nr_progetti`
- 1 `INSERT` in `PROGETTO`
- 1 `INSERT` in `FOTO`
- 1 `INSERT` in `REWARD`
- 1 `INSERT` in `PROGETTO_SOFTWARE`/`PROGETTO_HARDWARE`
- 1 `INSERT` in `PROFILO`/`COMPONENTE`

**Variabili**
- $f(Op_{1})=1$
- $w_{I}=1$
- $\alpha=2$
- $NC_{\text{write}}=6$
- $NC_{\text{read}}=0$

**Costo**
$$\begin{align*}
c_{1}(Op_{1})=1\cdot 1\cdot (2\cdot 6+0)=12
\end{align*}$$

#### Escludendo `nr_progetti`

**Logica**
- Identica al caso di sopra eccetto per il primo passo

**Procedura**
- 1 `INSERT` in `PROGETTO`
- 1 `INSERT` in `FOTO`
- 1 `INSERT` in `REWARD`
- 1 `INSERT` in `PROGETTO_SOFTWARE`/`PROGETTO_HARDWARE`
- 1 `INSERT` in `PROFILO`/`COMPONENTE`

**Variabili**
- Identiche al caso di sopra eccetto:
- $NC_{\text{write}}=5$

**Costo**
$$\begin{align*}
c_{2}(Op_{1})=1\cdot 1\cdot (2\cdot 5+0)=10
\end{align*}$$

### **Operazione 2**
---
$Op_{2}$: **Visualizzare (read)** tutti i progetti e tutti i finanziamenti **(1 volta/mese, batch)**.

#### Includendo `nr_progetti`

**Logica**
11. Leggi l'intera tabella `PROGETTO`
12. Leggi l'intera tabella `FINANZIAMENTO`

**Procedura**
- 10 `SELECT` in `PROGETTO`
- 30 `SELECT` in `FINANZIAMENTO`

**Variabili**
- $f(Op_{1})=1$
- $w_{B}=0.5$
- $\alpha=2$
- $NC_{\text{write}}=0$
- $NC_{\text{read}}=40$

**Costo**
$$\begin{align*}
c_{1}(Op_{2})=1\cdot 0.5\cdot (2\cdot 0+40)=20
\end{align*}$$

#### Escludendo `nr_progetti`

I calcoli rimangono invariati a quelli fatti di sopra.
#### Costo
$$\begin{align*}
c_{2}(Op_{2})=1\cdot 0.5\cdot (2\cdot 0+40)=20
\end{align*}$$

### **Operazione 3**
---
$Op_{3}$: **Contare (read)** il numero di progetti associati ad uno specifico utente **(3 volte/mese, batch)**.

#### Includendo `nr_progetti`

**Logica**
13. Leggi l'attributo `nr_progetti` dell'utente.

**Procedura** (assumendo che per "associati" si intenda progetti creati da un utente)
- 1 `SELECT` in `CREATORE`

**Variabili**
- $f(Op_{1})=3$
- $w_{B}=0.5$
- $\alpha=2$
- $NC_{\text{write}}=0$
- $NC_{\text{read}}=1$

**Costo**
$$\begin{align*}
c_{1}(Op_{3})=3\cdot 0.5\cdot (2\cdot 0+1)=1.5
\end{align*}$$

#### Escludendo `nr_progetti`

**Logica** (assumendo l'assenza di un indice efficiente)
14. Leggi l'intera tabella `PROGETTO`
15. Filtra laddove l'email del creatore del progetto corrente non corrisponde all'email del creatore per cui si sta facendo la query

**Procedura**
- 2 `SELECT` in `PROGETTO`, avendo scandito **10 entry**

**Variabili**
- $f(Op_{1})=3$
- $w_{B}=0.5$
- $\alpha=2$
- $NC_{\text{write}}=0$
- $NC_{\text{read}}=10$

**Costo**
$$\begin{align*}
c_{2}(Op_{3})=3\cdot 0.5\cdot (2\cdot 0+10)=15
\end{align*}$$

### **Conclusione**
---
#### Includendo `nr_progetti`

$$\begin{align*}
\displaystyle\sum_{T=1}^{3}c_{1}(Op_{T})= 12+20+1.5=33.5
\end{align*}$$

#### Escludendo `nr_progetti`
$$\begin{align*}
\displaystyle\sum_{T=1}^{3}c_{2}(Op_{T})=10+20+15=45
\end{align*}$$
#### **Speedup**
$$\begin{align*}
\frac{45}{33.5}=1.34
\end{align*}$$
#### **Analisi**

|            | **Includendo `nr_progetti`** | **Escludendo `nr_progetti`** |
| ---------- | ---------------------------- | ---------------------------- |
| $Op_{1}$   | 12                           | 10                           |
| $Op_{2}$   | 20                           | 20                           |
| $Op_{3}$   | 1.5                          | 15                           |
| **Totale** | 33.5                         | 45                           |

Osservando i costi di entrambi scenari, risulta che **includere `nr_progetti` sia l'approccio corretto**. Il costo per le prime due operazione è praticamente identico. Il guadagno principale nel mantenere `nr_progetti` deriva dalla differenza di costo per la terza ed ultima operazione, $Op_{3}$, che richiede di contare il numero di progetti associati ad un utente creatore nella piattaforma. Lo **speedup** infatti è **pari ad 1.34**, indicando un **guadagno di efficienza circa del 34% includendo la ridondanza** sulla base delle operazioni elencate.

**N.B.** Anche se ci fosse per l'ultima operazione un indice efficiente che tenga traccia dell'associazione tra progetti e i creatori di essi, allora l'assenza della ridondanza comporterebbe un costo di 3 (basta porre $NC_{\text{read}}=2$ invece che $10$), che è comunque **2x più costoso rispetto all'utilizzo della ridondanza** (costo di $1.5$). Tenendo questa considerazione a mente, man mano che **la piattaforma cresce e più utenti creatori creano e gestiscono più progetti**, la differenza (di 2x) diventa sempre più notevole, e pertanto **converrà comunque mantenere la ridondanza**.

**Includendo `nr_progetti`**, l'operazione è immediata, dovendo semplicemente leggere l'attributo `CREATORE.nr_progetti` dell'utente. Infatti, per $Op_{3}$, il costo è 10 volte minore tenendo conto di `nr_progetti`, ed il costo in memoria associato è trascurabile (tenendo conto la tabella di volumi fornita nella traccia), che presuppone in media 2 progetti per utente, e 5 utenti sulla piattaforma. Volendo ottimizzare ulteriormente il costo in memoria di `nr_progetti`, lo si può rappresentare in MySQL come un `TINYINT UNSIGNED`, che ha un costo di 1 byte e può rappresentare valori compresi in $[0,255]$ (valori negativi non hanno senso in questo contesto). In pratica però non ha senso un'ottimizzazione del genere, dunque lo rappresento come un

**Escludendo `nr_progetti`**, l'operazione, a differenza dello scenario di sopra, non ha accesso a `CREATORE.nr_progetti`, e deve pertanto scandire ogni progetto nella tabella `PROGETTO`, effettivamente leggendo ogni entry (10 in tutto), e verificando su ciascuna se l'email del creatore combacia con quella inserita nella query.

## **3.2. LISTA DELLE TABELLE**

**UTENTE**(<u>email</u>, password, nickname, nome, cognome, anno_nascita, luogo_nascita)

**ADMIN**(<u>email_utente</u>, codice_sicurezza)

**CREATORE**(<u>email_utente</u>, nr_progetti, affidabilità)

**PROGETTO**(<u>nome</u>, email_creatore, descrizione, budget, stato, data_inserimento, data_limite)

**FOTO**(<u>id</u>, <u>nome_progetto</u>, foto)

**REWARD**(<u>codice</u>, <u>nome_progetto</u>, descrizione, foto, min_importo)

**COMMENTO**(<u>id</u>, email_utente, nome_progetto, data, testo, risposta)

**PROGETTO_SOFTWARE**(<u>nome_progetto</u>)

**PROGETTO_HARDWARE**(<u>nome_progetto</u>)

**COMPONENTE**(<u>nome_componente</u>, <u>nome_progetto</u>, descrizione, quantità, prezzo)

**PROFILO**(<u>nome_profilo</u>, <u>nome_progetto</u>)

**SKILL**(<u>competenza</u>)

**FINANZIAMENTO**(<u>data</u>, <u>email_utente</u>, <u>nome_progetto</u>, codice_reward, importo)

**SKILL_CURRICULUM**(<u>email_utente</u>, <u>competenza</u>, livello_effettivo)

**SKILL_PROFILO**(<u>nome_profilo</u>, <u>competenza</u>, <u>nome_progetto</u>, livello_richiesto)

**PARTECIPANTE**(<u>email_utente</u>, <u>nome_progetto</u>, <u>nome_profilo</u>, stato)

## **3.3. LISTA DEI VINCOLI INTER-RELAZIONALI**

**ADMIN**.email_utente                                       → **UTENTE**.email
**CREATORE**.email_utente                                → **UTENTE**.email
**PROGETTO**.email_creatore                            → **UTENTE**.email
**COMMENTO**.email_utente                             → **UTENTE**.email
**FINANZIAMENTO**.email_utente                    → **UTENTE**.email
**PARTECIPANTE**.email_utente                       → **UTENTE**.email
**SKILL_CURRICULUM**.email_utente              → **UTENTE**.email

**FOTO**.nome_progetto                                    → **PROGETTO**.nome
**REWARD**.nome_progetto                              → **PROGETTO**.nome
**COMMENTO**.nome_progetto                       → **PROGETTO**.nome
**PROGETTO_SOFTWARE**.nome_progetto  → **PROGETTO**.nome
**PROGETTO_HARDWARE**.nome_progetto → **PROGETTO**.nome
**COMPONENTE**.nome_progetto                   → **PROGETTO**.nome
**FINANZIAMENTO**.nome_progetto              → **PROGETTO**.nome
**PARTECIPANTE**.nome_progetto                 → **PROGETTO**.nome
**PROFILO**.nome_progetto                             → **PROGETTO**.nome

**FINANZIAMENTO**.codice_reward                → **REWARD**.codice

**SKILL_PROFILO**.nome_profilo                     → **PROFILO**.nome_profilo
**PARTECIPANTE**.nome_profilo                     → **PROFILO**.nome_profilo

**SKILL_CURRICULUM**.competenza             → **SKILL**.competenza
**SKILL_PROFILO**.competenza                      → **SKILL**.competenza

# **4. NORMALIZZAZIONE**
---
## **4.1. ANALISI**

In questa sezione viene analizzato lo schema logico prodotto sulla base della terza, e FNBC forma normale.

### 3FN
Solo se per ogni dipendenza funzionale X→Y:
- X è una superchiave dello schema
**Oppure**
- Y appartiene ad una chiave candidata dello schema

### FNBC
Solo se per ogni dipendenza funzionale X→Y:
- X è una superchiave dello schema

Di seguito viene dimostrato che **ogni tabella proposta di sopra è in Forma Normale Boyce & Codd**.

#### `UTENTE`
- **R**(<u>email</u>, password, nickname, nome, cognome, anno_nascita, luogo_nascita)
- **F** = {email → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `ADMIN`
- **R**(<u>email_utente</u>, codice_sicurezza)
- **F** = {email_utente → codice_sicurezza}
- **3FN: ✅** / **FNBC: ✅**

#### `CREATORE`
- **R**(<u>email_utente</u>, nr_progetti, affidabilità)
- **F** = {email_utente → nr_progetti, affidabilità}
- **3FN: ✅** / **FNBC: ✅**

#### `PROGETTO`
- **R**(<u>nome</u>, email_creatore, descrizione, budget, stato, data_inserimento, data_limite)
- **F** = {nome → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `FOTO`
- **R**(<u>id</u>, <u>nome_progetto</u>, foto)
- **F** = {id, nome_progetto → foto}
- **3FN: ✅** / **FNBC: ✅**

#### `REWARD`
- **R**(<u>codice</u>, <u>nome_progetto</u>, descrizione, foto, min_importo)
- **F** = {codice, nome_progetto → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `COMMENTO`
- **R**(<u>id</u>, email_utente, nome_progetto, data, testo, risposta)
- **F** = {id → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `PROGETTO_SOFTWARE`
- **R**(<u>nome_progetto</u>)
- **F** = DF Banale
- **3FN: ✅** / **FNBC: ✅**

#### `PROGETTO_HARDWARE
- **R**(<u>nome_progetto</u>)
- **F** = DF Banale
- **3FN: ✅** / **FNBC: ✅**

#### `COMPONENTE`
- **R**(<u>nome_componente</u>, <u>nome_progetto</u>, descrizione, quantità, prezzo)
- **F** = {nome_componente, nome_progetto → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `PROFILO`
- **R**(<u>nome_profilo</u>, <u>nome_progetto</u>)
- **F** = DF Banale
- **3FN: ✅** / **FNBC: ✅**

#### `SKILL`
- **R**(<u>competenza</u>)
- **F** = DF Banale
- **3FN: ✅** / **FNBC: ✅**

#### `FINANZIAMENTO`
- **R**(<u>data</u>, <u>email_utente</u>, <u>nome_progetto</u>, codice_reward, importo)
- **F** = {data, email_utente, nome_progetto → OGNI ATTRIBUTO}
- **3FN: ✅** / **FNBC: ✅**

#### `SKILL_CURRICULUM`
- **R**(<u>email_utente</u>, <u>competenza</u>, livello_effettivo)
- **F** = {email_utente, competenza → livello_effettivo}
- **3FN: ✅** / **FNBC: ✅**

#### `SKILL_PROFILO`
- **R**(<u>nome_profilo</u>, <u>competenza</u>, <u>nome_progetto</u>, livello_richiesto)
- **F** = {nome_profilo, competenza, nome_progetto → livello_richiesto}
- **3FN: ✅** / **FNBC: ✅**

#### `PARTECIPANTE`
- **R**(<u>email_utente</u>, <u>nome_progetto</u>, <u>nome_profilo</u> stato)
- **F** = {email_utente, nome_progetto, nome_profilo → stato}
- **3FN: ✅** / **FNBC: ✅**

# **5. RIFLESSIONI**
---
## select the most important ones here to add to presentation (eg. deffo slide for importance of db robust structure and clarity of sp's)

### [[DB-PRJ-REPORT#TODO]]

- budget proj hardware >= somma componenti
	- new triggers to automatically adjust budget based on insert/delete/update of components
		- php notification of change in budget before operation finalised
	- can still manually adjust budget, as long as it stays >= sum component cost

- additional triggers for handling partecipazioni in the event of any skill profilo update, and automatic rejection of any user whose skill is inferior to requested level BY NEVER EVEN INSERTING THEM IN THE PARTECIPANTE TABLE AT ALL (see trg_rifiuta_candidatura_livello_effettivo_insufficiente)
	- fundamental point at the end because PARTECIPANTE.stato = 'rifiutato' IS ONLY APPLICABLE TO THOSE WHOSE SKILL >= REQUESTED, BUT THE PROJECT CREATOR EXPLICITLY REFUSED THEM

- multi-layer security check → redundant security > single line of defense php-level
	- "security in depth"

- profiles were global when PROFILO_PROGETTO was separate from SKILL_PROFILO, so I removed PROFILO_PROGETTO and added nome_progetto to PK of SKILL_PROFILO
	- Otherwise updates for livello_richiesto were problematic due to profiles being global and thus multiple proj might reference the same profilo but have different livello_richiesto

- finanziamento.codice_reward is defined at php-level when the user chooses finanziamento.importo and BEFORE the finanziamento record is submitted

- use of sql signal state 45000 to be able to notice at php-level any db issues

- testing stored procedures via dedicated sql demo fake data file

- the use of business fields / client-facing fields as PK for the tables (es. PROGETTO.nome) is not ideal because it may be subject to change (es. project owner may decide to rename the project at some point), so I think it would've been better to use something like an ID that is not subject to change for the db to reference as PK. However I opted not to so as to be faithful to the traccia

- was considering the possibility for a creator to delete his own project (sp_progetto_delete) but that would add significant complexity and considerations to make both db-level and real-life-level because
	- db-level would mean having to also wipe all FK mentioning, 1 problem example is the rewards tied to a user after he finances project → if project is wiped all his rewards would also be wiped unless add new table that does not include project name as PK, this only introduces more complexity to system
	- real-life-level there is the implication of fraud, can a creator just decide to delete the project and run away with all the financing of the users? no obviously
- **This has led to my realisation that a clear picture of the operations needed to be done before he starts designing the db is crucial**
- I have opted not to implement this as it was not required in the traccia and doing so would require restructuring the db schema and i am already a bit far into sql

- I initially had a ridiculous amount of operations that far exceeded what was requested in the list of operations of the traccia, but cut down significantly as it wouldve made the system, though more realistic, far too complex beyond the scope of the project. The most complex of which were always sp_X_update procedures
	- **scope creep in check... do not implement what was not asked of you to implement**
		- another example was originally wanting to containerise the project with docker, or use laravel framework for php... all great things but not required and would add significant overhead in complexity

- lots of SPs have common logic checks, I decided to modularise this by abstracting out the common logic checks into secondary helper SP's to improve readability and maintainability of the code
	- I followed the same philosophy and even file structure (checks first, action last) in php
	- idem but with triggers, removed all triggers (except for the ones asked in the traccia) i defined and instead integrated the logic into existing sp's, to improve maintainability of the code so as to ensure that everything that has to be known about what the sp affects in the db is immediately visible there

- With my main init file having grown to >2700 lines I decided to document it with very clear segmentations to make it clearer and more maintainable
	- highlight importance of clear structure, documentation, and necessity of transmitting clearly the purpose/intent of the SP etc

- while the existence check for the project might seem redundant from a pure data-integrity standpoint, it is valuable for providing a better, more controlled error response and enforcing additional business logic. EXAMPLE: The check for the project’s closed state is absolutely necessary for comments and financing operations because it’s not covered by the foreign key constraints.

- decomposing php site into components, actions, functions, and public (pages) to split page display from action logic, and components great for code reusability

# **6. FUNZIONALITÀ**
---
## **6.1. BACKEND (MySQL)**

In questa sezione si presenta una breve overview della struttura dei file che inizializzano la base di dati `BOSTARTER`.

### **Inizializzazione DB**

Il file di inizializzazione del database, `bostarter_init.sql`, si suddivide nelle seguenti parti principali:

#### `TABELLE`
- Si definiscono tutte le tabelle usate dal database.
#### `STORED PROCEDURES (HELPER)`
- Si definiscono tutte stored procedure di tipo secondario/helper utilizzate da altre stored procedure (primarie/main) per effettuare controlli di sicurezza, o a livello di applicazione invocate mediante la funzione mia `sp_invoke(...)`.
#### `STORED PROCEDURES (MAIN)`
- Si definiscono tutte le stored procedures di tipo primario/main, fungendo come interfaccia principale fra l'applicazione (sempre mediante `sp_invoke(...)` e il database per quasi ogni operazioni disponibile sulla piattaforma.
#### `VISTE`
- Si definiscono le tre viste richieste dal progetto.
#### `TRIGGERS`
- Si definiscono tutti i trigger richiesti dal progetto, con qualche aggiunta mia.
#### `EVENTI`
- Si definisce il solo evento richiesto dal progetto.

Le tabelle, attraverso vincoli inter-relazionali e check definiti a livello di attributo fungono già come un buon punto di partenza in termini di sicurezza e consistenza dei dati. Ho optato, però, di implementare un ulteriore livello di sicurezza centrale e robusto all'interno delle stored procedures definite nel file. Come menzionato di sopra, ho suddiviso le stored procedures in due categorie principali: Main e helper. Le stored procedure "main" performano operazioni richieste dalla piattaforma (es. aggiunta di una skill globale), e si appoggiano a stored procedures di tipo "helper" per verificare che ogni controllo di sicurezza per l'operazione passi (es. l'utente che aggiunge una skill globale deve essere admin).

### **Popolamento DB**

Il file di popolamento con dati fittizi per il database, `bostarter_demo.sql`, si suddivide nelle seguenti parti principali:

#### `UTENTE REGISTRATION (ALL)`
- Registrazione di tutti gli utenti finti nella piattaforma, con svariati admin, creatori, ed utenti regolari.
#### `SKILL INSERTION (ADMIN)`
- Inserimento di skill nella lista globale della piattaforma. Solo admin possono fare tale operazione.
#### `SKILL_CURRICULUM INSERTION (ALL)`
- Inserimento di skill nel proprio curriculum da parte di ogni utente, in base alle skill globali definite di sopra.
#### `PROGETTO INSERTION (CREATORE)`
- Inserimento da parte del creatore di progetti nella piattaforma.
#### `COMPONENTE OPERATIONS (CREATORE)`
- Nel caso di progetti hardware, inserimento da parte del creatore del progetto di componenti necessarie.
#### `PROFILO & SKILL_PROFILO OPERATIONS (CREATORE)`
- Nel caso di progetti software, inserimento da parte del creatore del progetto di profili e relative competenze al quale utenti possono candidarsi.
#### `COMMENTO OPERATIONS (ALL)`
- Inserimento di commenti su ciascun progetto della piattaforma; alcuni commenti dispongono di una risposta dal creatore.
#### `PARTECIPANTE OPERATIONS (ALL)`
- Inserimento da utenti per candidature di profili dei progetti, accettati/rifiutati dal creatore.
#### `FINANZIAMENTO OPERATIONS (ALL)`
- Inserimento di finanziamenti di progetti da parte di ogni utente.

In questo file vengono fatte chiamate delle stored procedures definite nel file di sopra per inserire i dati fittizi nella piattaforma. Ovviamente per il suo corretto funzionamento vengono fatte solo chiamate valide, e non vengono utilizzate qui stored procedures che rimuovono/aggiornano dati (ha più senso fare una dimostrazione di esso in sede d'esame).

### **Script**

Viene riportato in [[#**7.3. Script**|fondo alla relazione]] un bash script che inizializza e popola automaticamente la base di dati utilizzando i file di sopra. Per poter utilizzarlo correttamente basta:
1. Modificare nello script `MYSQL_PASS= <LA TUA PASSWORD>` con la propria password
2. Assicurarsi di avere entrambi i file sql (`bostarter_init.sql` & `bostarter_demo.sql`) presenti nella stessa directory dello script
3. (macOS) Eseguire su linea di comando, accertandosi che lo script abbia il permesso di eseguire: `./init_demo.sh`
	- Se tutto è andato a buon fine, l'ultimo messaggio sul terminale dovrebbe indicare: `-- BOSTARTER INIZIALIZZATO --`

## **6.2. FRONTEND (PHP)**

In questa sezione si presenta una breve overview della struttura della piattaforma BOSTARTER e delle sue funzionalità.

#### Struttura Generale

Di seguito la struttura generale del sito:
# COPY PASTE HERE THE DIR STRUCTURE OF THE WEBSITE FROM ABOVE!!!!!!!!!!!!!!!!!!

`/actions`: Le operazioni più complesse che permettono il funzionamento della piattaforma. Vengono invocate all'interno delle pagine.

`/components`: Componenti grafiche riutilizzabili/fisse in ogni pagina.

`/config`: Contiene le configurazioni necessarie per la piattaforma, come connessione al database.

`/functions`: Funzioni primitive / semi-primitive che eseguono controlli od operazioni semplici comuni usate per il funzionamento delle pagine.

`/public`: Le pagine php visibili al client.

#### Struttura File

La struttura generale di ogni file php nelle directory `public/` e `actions/` è la seguente:

- `/public`:
```php
<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// ...

// === DATABASE ===
// ...
?>

// <HTML>
```
A capo di ogni file viene posta la sezione di configurazione (`=== CONFIG ===`), che chiama `session_start()` per recuperare le variabili di sessioni ed importa il file `config.php` contenente la connessione al database. Di seguito si hanno i controlli di sicurezza preliminari (`=== CHECKS ===`) relativi alla pagina specifica, e per ultimo si recuperano dal database (`=== DATABASE ===`) dati necessari mediante stored procedures. In fondo si ha la pagina html, con php minimale (tanto quanto necessario per del conditional rendering).

- `/actions`:
```php
<?php
// === CONFIG ===
session_start();
require '../config/config.php';

// === CHECKS ===
// ...

// === ACTION ===
try {
    $in = [
        'p_X' => $X,
        // ...
    ];

    sp_invoke('X', $in);
} catch (PDOException $ex) {
	// Error, redirect alla pagina X
    redirect(
        false,
        "Errore X: " . $ex->errorInfo[2],
        '../public/X.php'
    );
}

// ...

// Success, redirect alla pagina X
redirect(
    true,
    'X effettuato con successo/correttamente.',
    '../public/X.php'
);
```
Risulta immediato che la struttura del file sia simile a quella vista di sopra. L'unica differenza è la seguente: Invece di avere una sezione per il recupero di dati dal database (`=== DATABASE ===`) e in fondo pagina html (`<HTML>`), essendo un file che deve fare un'operazione su una pagina, si ha semplicemente una sezione che prepara ed invoca una stored procedure (`=== ACTION ===`), con successful od error redirect in base alla situazione specifica.

### **AUTENTICAZIONE UTENTE**
- `login.php`
- `register.php`

Il landing page della piattaforma, `index.php`, verifica se l'utente si è già autenticato controllando la variabile di sessione `$_SESSION['user_email']` e se sì allora viene reindirizzato alla homepage, `home.php`, altrimenti viene reindirizzato alla pagina di login, `login.php` per autenticarsi.

Se dispone di un account esistente sulla piattaforma (email e password) allora può autenticarsi, altrimenti clicca su Registra e continua con la procedura per creare il proprio account. In fase di login l'utente può anche autenticarsi come amministratore se dispone del codice di sicurezza proprio.

![[login.png]]

Verrà resa disponibile in sede d'esame una sezione di autologin per poter passare rapidamente fra utenti e testare diverse funzionalità della piattaforma.

![[autologin.png]]

La struttura della pagina di registrazione è molto simile a quella di login per mantenere un look consistente e prevedibile per l'utente, con la sola differenza che contiene alcuni campi aggiuntivi per i propri dati personali.

![[register.png]]

In fase di registrazione, l'utente ha la possibilità di segnarsi come un creatore e/o admin della piattaforma, cliccando sulle checkbox sopra al submit button della registrazione. In tal caso, verrà inserito nel DB anche come `CREATORE` / `ADMIN`.

### **HOMEPAGE**
- `home.php`

### **STATISTICHE**
- `statistiche.php`

La pagina delle statistiche è relativamente semplice, chiamando i dati presenti nelle 3 viste definite nel database, e rendendole visibili nella forma di tabelle.

![[statistiche.png]]

Non sono previste alcune operazioni da nessun utente su questa pagina; i dati prodotti dalle viste vengono aggiornati in tempo reale, ricaricando la pagina.

### **CURRICULUM**

### **FINANZIAMENTI**

### **PROGETTI**

### **DETTAGLI PROGETTO**

### **CANDIDATURE**

## **6.3. LOGGING (MongoDB)**

### for each table of MySQL create a dedicated MongoDB collection of the same name and log there whenever modification to the MySQL table is made. In essence just need to add a mongodb call at the end of each function in php logging whatever data and variables where passed as argument

# **7. APPENDICE**
---
## **7.1. Inizializzazione DB**
In seguito viene riportato il codice SQL completo utilizzato per la generazione della base di dati **BOSTARTER** e delle relative funzionalità (procedure, viste, trigger, eventi):

```sql
bostarter_init.sql
```

## **7.2. Popolamento DB**
Il codice SQL per la demo con popolamento di dati fittizi, usando le stored procedures per testare il loro funzionamento: 

```sql
bostarter_demo.sql
```

## **7.3. Script**
Di seguito anche un breve script che invoca i due file di sopra, per inizializzare il DB e popolare la piattaforma con i dati fittizi. Lo script, inoltre, invoca un file php che popola la piattaforma con foto fittizie per i progetti, componenti, ecc.. Mi raccomando per il corretto funzionamento di sovrascrivere la variabile `MYSQL_PASS` con la propria password:

```sh
init_demo.sh
```
