# TODO
---
- [ ] check for any leftover TODO's in project
- [ ] check for any blatant gpt english comments
- [ ] PASTE FULL SQL INIT FILE TO [[#**7.1. Inizializzazione DB**]]
	- [ ] read regole if should paste also init and demo
- [ ] review entire report and ensure consistent tables, attributes etc
	- [ ] update LISTA DELLE OPERAZIONI MAYBE!!!!!
- [ ] create README.md for repo to explain how to setup and run project
- [ ] redo screenshots of funzionalità section once website complete
- [ ] export to pdf using pandoc
	- [ ] potentially need to fix mismatching links text for correct rendering
	- [ ] ensure clickable links for ToC




# email prof ask if OK that when creatore updates profile's skill if existing ACCEPTED partecipanti should be rifiutato (as is currently, and thus barred from re-applying) or if should just delete the candidatura (and thus allow them to reapply)





# ask claude if can split componente_modifica and profilo_modifica each into two separate php files, one for adding new profilo/componente and the other for modifying existing profilo/componente





# when done refactoring /public w ActionPipeline, push and ask claude to generate multiline header comments same as /actions but like this:
```php
/**
 * PAGE:
 * 
 * ACTIONS:
 *
 * PURPOSE:
 *
```
- ## and also add multiline docs above all /functions etc


## refactor /componenti too, ensure that even after /public refactor is complete that variable names originating from /public and used in /componenti align


## fix anno_nascita registrazione check to look at the curdate - 18yrs in days to evaluate if user is 18 (better precision otherwise 17yr olds X months can still sign up)




## use generate_url() to generate redirect URLs in /public
- leave form tags leading to /actions alone? or refactor generate_url to also include actions in routes?

# logs.php
- remove logs recenti section and just have the table beneath showing w all the logs, and above it have the table selector area with an additional button Reset that resets the filter search (redirects back to plain logs.php w no query params)
- create checkbox button to filter for errors only in logs.php


# comb thru init sql and for each sp verify it is being used, if not remove it
- sp_util_progetto_owner_exists



## can I use a ActionPipeline in checks.php?



### (HELLA OPTIONAL) try to see with docker if can serve on php files from php/public, while having everything work like public pages can still load php/config, php/actions, php/components, php/public/libs etc
- ### correct <\a> and <\form> tag paths in php code


## see claude chat on abstracting reusable php components
- ### create reusable components for cards like reward cards, see if more shared visual components, or if can turn standardise platform with new reusable components



## standardise POST var names like 'nome_progetto' instead of 'nome' when passing project name via post, etc
- #### need to update progetto_aggiorna attr to be passed via POST no GET
- ensure they are also specific like id_foto instead of id
- `if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != UPLOAD_ERR_OK) {` is how foto upload handling is checked



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
│   ├── componente_delete.php
│   ├── componente_insert.php
│   ├── componente_update.php
│   ├── finanziamento_insert.php
│   ├── foto_delete.php
│   ├── login_handler.php
│   ├── logout.php
│   ├── profilo_delete.php
│   ├── profilo_insert.php
│   ├── profilo_nome_update.php
│   ├── progetto_budget_update.php
│   ├── progetto_descrizione_update.php
│   ├── progetto_insert.php
│   ├── register_handler.php
│   ├── reward_insert.php
│   ├── skill_curriculum_delete.php
│   ├── skill_curriculum_insert.php
│   ├── skill_curriculum_update.php
│   ├── skill_insert.php
│   ├── skill_profilo_delete.php
│   ├── skill_profilo_insert.php
│   ├── skill_profilo_update.php
│   ├── skill_update.php
│   └── utente_convert_creatore.php
├── components/
│   ├── error_alert.php
│   ├── footer.php
│   ├── header.php
│   └── success_alert.php
├── config/
│   └── config.php
├── functions/
│   ├── checks.php
│   ├── redirect.php
│   └── sp_invoke.php
├── public/
│   ├── libs/
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

- having to reason over aspects of the project which beforehand seemed obvious but in hindsight paved way for significant reflection on how project works/should handle things
	- budget proj hardware >= somma componenti
		- new triggers to automatically adjust budget based on insert/delete/update of components
			- php notification of change in budget before operation finalised
		- can still manually adjust budget, as long as it stays >= sum component cost
	- can an admin register?
	- profili as a generic entity with infinite instances vs concrete entity with only 1 available instance / profile
	- finanziamenti once a day (mysql DATE) vs once per second (mysql DATETIME)

- additional logic in sp's for handling partecipazioni in the event of any skill profilo update, and automatic rejection of any user whose skill is inferior to requested level BY NEVER EVEN INSERTING THEM IN THE PARTECIPANTE TABLE AT ALL
	- fundamental point at the end because PARTECIPANTE.stato = 'rifiutato' IS ONLY APPLICABLE TO THOSE WHOSE SKILL >= REQUESTED, BUT THE PROJECT CREATOR EXPLICITLY REFUSED THEM

- multi-layer security check → redundant security > single line of defense php-level
	- "security in depth"
		- reasonable refactoring maintaining only checks which can be made purely in php while removing redundant ones which already baked into primary sp and require additional db call

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

- lots of SPs have common logic checks, I decided to modularise this by abstracting out the common logic checks into secondary helper SP's to improve readability and maintainability of the code
	- I followed the same philosophy and even file structure (checks first, action last) in php
	- idem but with triggers, removed all triggers (except for the ones asked in the traccia) i defined and instead integrated the logic into existing sp's, to improve maintainability of the code so as to ensure that everything that has to be known about what the sp affects in the db is immediately visible there

- With my main init file having grown to >2700 lines I decided to document it with very clear segmentations to make it clearer and more maintainable
	- highlight importance of clear structure, documentation, and necessity of transmitting clearly the purpose/intent of the SP etc
	- removing custom triggers and maintaining only the ones strictly required by project traccia. I opted to keep all the logic handling in sp's as it centralises all the logic checks and actions performed to the most relevant areas (eg. when a creatore updates a skill profilo level i want to see only the sp that he invokes and inside of it i should expect to infer every possible side-effect/change in db that emerges as a result of that operation)

- while the existence check for the project might seem redundant from a pure data-integrity standpoint, it is valuable for providing a better, more controlled error response and enforcing additional business logic. EXAMPLE: The check for the project’s closed state is absolutely necessary for comments and financing operations because it’s not covered by the foreign key constraints.
	- coming back to this from a near complete project pov im cleaning up unnecessary/redundant validations but maintaining key ones for php. Reasonable approach as reduces php code, avoids logical duplication of validations, eliminating the ones which would rely on sp calls that are already being made in the primary sp, but maintaining ones which do not rely on a sp call and can catch errors earlier on preventing unnecessary sp call

- decomposing php site into components, actions, functions, and public (pages) to split page display from action logic, and components great for code reusability and security

- navigating unforeseen complexity: apache web server permissions, configuring php environment, getting mongodb extension working → All lead to me learning a bit about docker and containerising the whole project, ensuring also universal portability of the project

- ActionPipeline class
	- cleaning up code significantly, removing boilerplate/excess overhead code, much cleaner interface, centralising data management (\$context array) and execution (methods of class) while also baking in logging functionality in a decoupled manner (class methods use fail and success functions, and these 2 functions redirect + log, but the primitive redirect function does not perform logging and is used elsewhere in code where logging unnecessary)


# **6. FUNZIONALITÀ**
---
## **PREREQUISITI**

#### **Requisiti Software**
- **Docker** e **Docker Compose**: Per la containerizzazione e l'orchestrazione
- **MySQL 8.0+**: Database per i dati principali dell'applicazione
- **MongoDB**: Database secondario per la funzionalità di logging
- **PHP 8.2** con estensioni:
    - `pdo_mysql`: Per la connettività MySQL
    - `mongodb`: Per la connettività MongoDB
- **Apache Web Server**: Per servire l'applicazione PHP

#### **Configurazione Ambiente**
- File `.env` con variabili correttamente configurate:
    - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
    - `MONGO_URI`, `MONGO_DB`

```
# MySQL Configuration
DB_HOST=db
DB_NAME=BOSTARTER
DB_USER=root
DB_PASS=<YOUR_PASSWORD>

# MongoDB Configuration
MONGO_URI=mongodb://mongodb:27017
MONGO_DB=BOSTARTER_LOG
```

Basta rimpiazzare `<YOUR_PASSWORD>` con la propria password per accedere a MySQL.

#### **Requisiti di Sistema**
- Porta `8080` disponibile per l'accesso web
- Porte `3307` e `27017` disponibili per l'accesso ai database

## **6.1. BACKEND (MySQL)**
### **INIZIALIZZAZIONE**

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
- Si definiscono tutti i trigger richiesti dal progetto.
#### `EVENTI`
- Si definisce il solo evento richiesto dal progetto.

Le tabelle, attraverso vincoli inter-relazionali e check definiti a livello di attributo fungono già come un buon punto di partenza in termini di sicurezza e consistenza dei dati. Ho optato, però, di implementare un ulteriore livello di sicurezza centrale e robusto all'interno delle stored procedures definite nel file. Come menzionato di sopra, ho suddiviso le stored procedures in due categorie principali: Main e helper. Le stored procedure "main" performano operazioni richieste dalla piattaforma (es. aggiunta di una skill globale), e si appoggiano a stored procedures di tipo "helper" per verificare che ogni controllo di sicurezza richiesto per l'operazione sia garantito (es. l'utente che aggiunge una skill globale deve essere admin).

### **POPOLAMENTO**

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

## **6.2. FRONTEND (PHP)**
### **STRUTTURA GENERALE**

Di seguito la struttura generale del sito:
# COPY PASTE HERE THE DIR STRUCTURE OF THE WEBSITE FROM ABOVE!!!!!!!!!!!!!!!!!!

In particolare...
- `/actions`: Le operazioni più complesse (generalmente associate ad una o più "main" stored procedures) che permettono il funzionamento della piattaforma. Vengono invocate all'interno delle pagine mediante i form.
- `/components`: Componenti grafiche riutilizzabili in una o più pagine.
- `/config`: Contiene le configurazioni necessarie per la piattaforma, come connessione al database.
- `/functions`: Funzioni primitive / semi-primitive che eseguono controlli od operazioni semplici e comuni, usate per il funzionamento delle pagine.
- `/public`: Le pagine php visibili al client.

### **STRUTTURA FILE**

La struttura generale di ogni file php nelle directory `public/` e `actions/` è la seguente:

- `/public`:

```php
<?php
// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VALIDATION ===
// ...

// === DATA ===
// ...
?>

// <HTML>
```
A capo di ogni file viene posta la sezione di configurazione (`=== SETUP ===`), che chiama `session_start()` per recuperare le variabili di sessioni, importa il file `config.php` contenente la connessione al database, e controlla se l'utente si è autenticato. Di seguito i controlli di sicurezza preliminari (`=== VALIDATION ===`) relativi alla pagina specifica, e per ultimo si recuperano dal db (`=== DATA ===`) i dati necessari mediante stored procedures. In fondo si ha la pagina html, con php minimale (tanto quanto necessario per del conditional rendering).

- `/actions`:

```php
<?php
/**
 * ACTION: ...
 * PERFORMED BY: ...
 * UI: ...
 * 
 * PURPOSE:
 * - ...
 * - Se l'operazione va a buon fine, ...
 * - Per maggiori dettagli, vedere la documentazione ...
 *
 * VARIABLES:
 * - ...
 */

// === SETUP ===
session_start();
require '../config/config.php';
check_auth();

// === VARIABLES ===
check_POST(...);
...

// === CONTEXT ===
$context = [
	...
];
$pipeline = new EventPipeline($context);

// === VALIDATION ===
$pipeline->check(...);

// === ACTION ===
$pipeline->invoke();

// === SUCCESS ===
$pipeline->continue();
```
Risulta immediato che la struttura dei file in `/action` segue un pattern uniforme che sfrutta la classe `ActionPipeline` per gestire validazioni, esecuzione e redirect in modo consistente. Ogni file è organizzato in sezioni chiaramente delimitate:

- **`=== SETUP ===`**: Inizializzazione della sessione e inclusione delle dipendenze
- **`=== VARIABLES ===`**: Estrazione e validazione dei parametri di input
- **`=== CONTEXT ===`**: Creazione di un contesto dell'operazione che include collezione, azione, redirect e procedura da eseguire
- **`=== VALIDATION ===`**: Controlli di validazione utilizzando il pattern pipeline che interrompe l'esecuzione al primo errore
- **`=== ACTION ===`**: Invocazione della stored procedure per eseguire l'operazione sul database
- **`=== SUCCESS ===`**: Gestione del successo con logging dell'evento e redirect

Questa architettura semplifica la gestione degli errori e il logging centralizzando queste funzionalità nella classe `ActionPipeline`. Rispetto alla struttura in `/public`, non sono necessarie sezioni per il recupero dati (`=== DATA ===`) o per il rendering HTML (`<HTML>`), poiché questi file si occupano esclusivamente di operazioni atomiche sul database con redirect automatica in base all'esito.

Il pattern migliora la mantenibilità e la consistenza del codice grazie all'incapsulamento della logica di controllo e gestione errori, permettendo una separazione chiara tra validazione, esecuzione e gestione del risultato.

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

### **HOME**
- `home.php`

La pagina di home funge come luogo centrale della piattaforma, dove l'utente ha modo di visualizzare alcune informazioni del proprio account, e di recarsi in altre sezioni specifiche del sito. Il metodo principale di navigazione fra sezioni principali della piattaforma è mediante la navbar, ed all'interno di ciascuna sezione si hanno bottoni specifici che reindirizzano in sottopagine del sito.

![[home.png]]

### **STATISTICHE**
- `statistiche.php`

La pagina delle statistiche è relativamente semplice, chiamando i dati presenti nelle 3 viste definite nel database, e rendendole visibili nella forma di tabelle.

![[statistiche.png]]

Non sono previste alcune operazioni da nessun utente su questa pagina; i dati prodotti dalle viste vengono aggiornati in tempo reale, ricaricando la pagina.

### **CURRICULUM**
- `curriculum.php`

La pagina curriculum consente agli utenti di gestire le proprie competenze professionali. Ogni utente può aggiungere skill selezionandole dalla lista globale e specificando il proprio livello di competenza (da 0 a 5). L'utente può anche modificare (il livello) o rimuovere proprie skill esistenti.

![[curriculum.png]]

Per gli amministratori, è disponibile una sezione aggiuntiva per gestire la lista globale delle competenze disponibili sulla piattaforma. Questa funzionalità permette di aggiungere nuove competenze o modificare (il nome) quelle esistenti.

![[curriculum_admin.png]]

### **FINANZIAMENTI**
- `finanziamenti.php`
- `finanziamento_conferma.php`

La pagina dei finanziamenti mostra la cronologia di tutti i finanziamenti effettuati dall'utente. Per gli utenti creatori, è presente anche una sezione che visualizza i finanziamenti ricevuti dai propri progetti.

![[finanziamenti.png]]

Quando un utente decide di finanziare un progetto dalla pagina di dettaglio, viene reindirizzato a `finanziamento_conferma.php` dove può selezionare una reward tra quelle disponibili in base all'importo che intende donare.

### **PROGETTI**
- `progetti.php`
- `progetto_crea.php`

La pagina progetti visualizza tutti i progetti disponibili sulla piattaforma, con informazioni riassuntive su ciascuno: nome, tipo (software/hardware), stato (aperto/chiuso), percentuale di completamento, e giorni rimanenti alla chiusura.

![[progetti.png]]

Gli utenti creatori visualizzano anche un bottone "Crea Progetto" che consente loro di creare un nuovo progetto, mentre gli utenti regolari vedono un bottone "Diventa Creatore" che permette loro di acquisire il ruolo di creatore sulla piattaforma.

La pagina `progetto_crea.php` guida il creatore attraverso il processo di inserimento dei dati del progetto: nome, descrizione, budget, data limite e tipo (software/hardware). Il creatore ha modo di inserire ulteriori informazioni riguardanti il progetto nell'apposita pagina di dettaglio (vedi sezione successiva).

![[progetto_crea.png]]

### **DETTAGLI PROGETTO**
- `progetto_dettagli.php`

La pagina di dettaglio progetto è il centro operativo dove convergono la maggior parte delle funzionalità della piattaforma. Visualizza tutte le informazioni relative al progetto selezionato: descrizione, foto, budget, somma finanziamenti ricevuti, reward disponibili, componenti o profili richiesti in base al tipo, ed in fondo i commenti del progetto.

![[progetto_dettagli.png]]

**Funzionalità principali:**
- Finanziamento del progetto (se aperto)
- Visualizzazione delle reward disponibili
- Gestione delle componenti (per progetti hardware)
- Gestione dei profili e candidature (per progetti software)
- Sezione commenti con possibilità di risposta per il creatore

I creatori del progetto visualizzano anche bottoni di modifica per ogni sezione, che reindirizzano alle pagine di aggiornamento specifiche.

### **CANDIDATURE**
- `candidature.php`

La pagina candidature mostra tutte le candidature inviate dall'utente per partecipare ai progetti software. Per gli utenti creatori, è presente anche una sezione che visualizza le candidature ricevute dai propri progetti.

![[candidature.png]]

Le candidature possono avere tre stati: "in attesa", "accettato" o "rifiutato". I creatori possono gestire le candidature ricevute, accettandole o rifiutandole. Un utente può candidarsi a un profilo solo se possiede tutte le competenze richieste con livello uguale o superiore a quello richiesto.

### **GESTIONE PROFILI/COMPONENTI**
- `progetto_aggiorna.php`

La pagina di gestione profili/componenti consente ai creatori di gestire i dettagli specifici del proprio progetto.

**Per progetti software:**
- Creazione, modifica ed eliminazione di profili richiesti
- Definizione delle competenze necessarie per ciascun profilo e relativo livello richiesto

![[gestione_profili.png]]

Eventuali modifiche ai requisiti di un profilo possono comportare il rifiuto automatico di candidature esistenti se i candidati non soddisfano più i nuovi requisiti.

**Per progetti hardware:**
- Aggiunta, modifica ed eliminazione di componenti necessari
- Definizione di quantità e prezzo di ciascun componente

![[gestione_componenti.png]]

Eventuali modifiche alla quantità o prezzo di componenti esistenti, o inserimento di nuovi, possono comportare l'aumento automatico del budget del progetto, se la somma del costo dei componenti eccede il budget attuale.

### **LOGS**
- `logs.php`

Gli utenti con privilegi di amministratore hanno accesso ad una pagina aggiuntive per la visualizzazione dei log di sistema, reperiti da MongoDB.
La pagina logs permette agli amministratori di monitorare tutte le attività sulla piattaforma, visualizzando le operazioni effettuate dagli utenti, suddivise per collezioni MongoDB corrispondenti alle tabelle del database.

![[logs.png]]

## **6.3. LOGGING (MongoDB)**
### **Overview**

La piattaforma adotta un sistema di logging centralizzato basato su MongoDB, che consente di tracciare e registrare in tempo reale tutti gli eventi generati dagli utenti. Ogni interazione, che si tratti di un login, una registrazione o altre azioni, viene memorizzata nel database MongoDB, facilitando così il monitoraggio, l’analisi e la risoluzione di eventuali problemi.

Nei file PHP che gestiscono le azioni degli utenti (`/actions`), il logging (e redirect) avviene sempre con uno dei due scenari:
- Fallimento: Il logging e redirect passano per la funzione `fail()` laddove è stata bloccata l'operazione (es. non sono stati superati i controlli di sicurezza)
- Successo: Il logging a redirect avvengono in fondo al file, e passano per la funzione `success()`.

### **Accesso**

I log sono disponibili a tutti gli utenti amministratori della piattaforma, e possono essere visualizzati nell'apposita pagina dei logs (`public/logs.php`).

Se si desidera accedere esternamente ai database (MySQL e MongoDB) presenti all’interno del container, è sufficiente eseguire uno dei seguenti comandi sul terminale (per MySQL, sostituire `<DB_HOST>` e `<DB_PASS>` con le variabili definite nel file `.env`):
```
# MySQL
docker exec -it bostarter-db-1 mysql -u<DB_HOST> -p<DB_PASS> BOSTARTER;

# MongoDB
docker exec -it bostarter-mongodb-1 mongosh BOSTARTER_LOG;
```

# **7. APPENDICE**
---
## **7.1. INIZIALIZZAZIONE**

Di seguito viene riportato il codice SQL completo utilizzato per la generazione della base di dati **BOSTARTER** e delle relative funzionalità (procedure, viste, trigger, eventi):

```sql
bostarter_init.sql
```

## **7.2. POPOLAMENTO**

Di seguito viene riportato il codice SQL completo per la demo con popolamento di dati fittizi, usando le stored procedures definite nel punto di sopra: 

```sql
bostarter_demo.sql
```

## **7.3. SCRIPT**

Di seguito viene illustrato il funzionamento dello script di inizializzazione della piattaforma:

```sh
init.sh
```
#### 1. **Verifica del file `.env`:**
- Lo script inizia controllando se esiste il file `.env` nella root del progetto. Se non viene trovato, il processo si interrompe con un messaggio d’errore che invita a creare e configurare correttamente tale file.

#### 2. **Installazione delle dipendenze:**
- Lo script entra nella directory `/php` per gestire le dipendenze relative a PHP e Node.js.

#### 3. **Avvio dei container Docker:**
- Viene eseguito `docker-compose down -v` per arrestare e rimuovere eventuali container e volumi esistenti.
- Viene eseguito `docker-compose up --build -d` per ricostruire e avviare i container.

Se si vede comparire sul terminale il seguente log, allora l'inizializzazione della piattaforma è andata a buon fine:
```sh
web-1      | === BOSTARTER INIZIALIZZATO. PIATTAFORMA PRONTA! ===
```

Questo script automatizza l’intero processo di configurazione, garantendo che tutte le dipendenze siano installate e che l’ambiente Docker sia correttamente ricostruito, rendendo l’avvio della piattaforma semplice e veloce.