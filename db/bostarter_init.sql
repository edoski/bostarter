-- ==================================================
--                  BOSTARTER INIT
-- ==================================================

DROP DATABASE IF EXISTS BOSTARTER;
CREATE DATABASE BOSTARTER;
USE BOSTARTER;

-- ==================================================
-- TABELLE
-- ==================================================

-- 1. UTENTE
CREATE TABLE UTENTE (
	email         VARCHAR(100) NOT NULL,
	password      VARCHAR(255) NOT NULL,
	nickname      VARCHAR(50)  NOT NULL UNIQUE CHECK ( LENGTH(nickname) > 0 ), -- Minimo 1 carattere
	nome          VARCHAR(50)  NOT NULL,
	cognome       VARCHAR(50)  NOT NULL,
	anno_nascita  INT          NOT NULL CHECK ( anno_nascita < 2007 ),         -- età > 18
	luogo_nascita VARCHAR(50)  NOT NULL,
	PRIMARY KEY (email)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 2. ADMIN
CREATE TABLE ADMIN (
	email_utente     VARCHAR(100) NOT NULL,
	codice_sicurezza VARCHAR(100) NOT NULL CHECK ( LENGTH(codice_sicurezza) >= 8 ), -- Minimo 8 caratteri
	PRIMARY KEY (email_utente),
	CONSTRAINT fk_admin_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 3. CREATORE
CREATE TABLE CREATORE (
	email_utente VARCHAR(100) NOT NULL,
	nr_progetti  INT UNSIGNED  DEFAULT 0,
	affidabilita DECIMAL(5, 2) DEFAULT 0.00 CHECK ( affidabilita BETWEEN 0.00 AND 100.00 ), -- Progetti finanziati / Progetti creati
	PRIMARY KEY (email_utente),
	CONSTRAINT fk_creatore_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 4. PROGETTO
CREATE TABLE PROGETTO (
	nome             VARCHAR(100)   NOT NULL CHECK ( LENGTH(nome) > 0 ),        -- Minimo 1 carattere
	email_creatore   VARCHAR(100)   NOT NULL,
	descrizione      TEXT           NOT NULL CHECK ( LENGTH(descrizione) > 0 ), -- Minimo 1 carattere
	budget           DECIMAL(10, 2) NOT NULL CHECK ( budget > 0 ),
	stato            ENUM ('aperto','chiuso') DEFAULT 'aperto',
	data_inserimento DATE           NOT NULL  DEFAULT CURRENT_DATE,
	data_limite      DATE           NOT NULL,
	PRIMARY KEY (nome),
	CONSTRAINT fk_progetto_creatore
		FOREIGN KEY (email_creatore)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 5. PROGETTO_SOFTWARE
CREATE TABLE PROGETTO_SOFTWARE (
	nome_progetto VARCHAR(100) NOT NULL,
	PRIMARY KEY (nome_progetto),
	CONSTRAINT fk_psw_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 6. PROGETTO_HARDWARE
CREATE TABLE PROGETTO_HARDWARE (
	nome_progetto VARCHAR(100) NOT NULL,
	PRIMARY KEY (nome_progetto),
	CONSTRAINT fk_phw_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 7. FOTO
CREATE TABLE FOTO (
	id            INT          NOT NULL AUTO_INCREMENT,
	nome_progetto VARCHAR(100) NOT NULL,
	foto          MEDIUMBLOB   NOT NULL, -- Max 16 MB per foto
	PRIMARY KEY (id, nome_progetto),
	CONSTRAINT fk_foto_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 8. REWARD
CREATE TABLE REWARD (
	codice        VARCHAR(50)    NOT NULL,
	nome_progetto VARCHAR(100)   NOT NULL,
	descrizione   TEXT           NOT NULL CHECK ( LENGTH(descrizione) > 0 ), -- Minimo 1 carattere
	foto          MEDIUMBLOB     NOT NULL,
	min_importo   DECIMAL(10, 2) NOT NULL CHECK ( min_importo > 0 ),
	PRIMARY KEY (codice, nome_progetto),
	CONSTRAINT fk_reward_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 9. COMPONENTE
CREATE TABLE COMPONENTE (
	nome_componente VARCHAR(100)   NOT NULL CHECK ( LENGTH(nome_componente) > 0 ), -- Minimo 1 carattere
	nome_progetto   VARCHAR(100)   NOT NULL,
	descrizione     TEXT           NOT NULL CHECK ( LENGTH(descrizione) > 0 ),     -- Minimo 1 carattere
	quantita        INT            NOT NULL CHECK ( quantita > 0 ),                -- Business Rule #3
	prezzo          DECIMAL(10, 2) NOT NULL CHECK ( prezzo > 0 ),
	PRIMARY KEY (nome_componente, nome_progetto),
	CONSTRAINT fk_comp_phw
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO_HARDWARE (nome_progetto)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 10. PROFILO
CREATE TABLE PROFILO (
	nome_profilo  VARCHAR(100) NOT NULL CHECK ( LENGTH(nome_profilo) > 0 ), -- Minimo 1 carattere
	nome_progetto VARCHAR(100) NOT NULL,
	PRIMARY KEY (nome_profilo, nome_progetto),
	CONSTRAINT fk_profilo_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 11. SKILL
CREATE TABLE SKILL (
	competenza VARCHAR(100) NOT NULL CHECK ( LENGTH(competenza) > 0 ), -- Minimo 1 carattere
	PRIMARY KEY (competenza)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 12. FINANZIAMENTO
CREATE TABLE FINANZIAMENTO (
	data          DATE           NOT NULL DEFAULT CURRENT_DATE,
	email_utente  VARCHAR(100)   NOT NULL,
	nome_progetto VARCHAR(100)   NOT NULL,
	codice_reward VARCHAR(50)    NOT NULL,
	importo       DECIMAL(10, 2) NOT NULL CHECK ( importo > 0 ),
	PRIMARY KEY (data, email_utente, nome_progetto),
	CONSTRAINT fk_fin_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_fin_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_fin_reward
		FOREIGN KEY (codice_reward, nome_progetto)
			REFERENCES REWARD (codice, nome_progetto)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 13. COMMENTO
CREATE TABLE COMMENTO (
	id            INT          NOT NULL AUTO_INCREMENT,
	email_utente  VARCHAR(100) NOT NULL,
	nome_progetto VARCHAR(100) NOT NULL,
	data          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
	testo         TEXT         NOT NULL CHECK ( LENGTH(testo) > 0 ), -- Minimo 1 carattere
	risposta      TEXT         NULL,                                 -- Business Rule #8
	PRIMARY KEY (id),
	CONSTRAINT fk_com_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_com_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 14. SKILL_CURRICULUM
CREATE TABLE SKILL_CURRICULUM (
	email_utente      VARCHAR(100) NOT NULL,
	competenza        VARCHAR(100) NOT NULL,
	livello_effettivo TINYINT      NOT NULL CHECK ( livello_effettivo BETWEEN 0 AND 5 ), -- Business Rule #1
	PRIMARY KEY (email_utente, competenza),
	CONSTRAINT fk_skcurr_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_skcurr_skill
		FOREIGN KEY (competenza)
			REFERENCES SKILL (competenza)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 15. SKILL_PROFILO
CREATE TABLE SKILL_PROFILO (
	nome_profilo      VARCHAR(100) NOT NULL,
	competenza        VARCHAR(100) NOT NULL,
	nome_progetto     VARCHAR(100) NOT NULL,
	livello_richiesto TINYINT      NOT NULL CHECK ( livello_richiesto BETWEEN 0 AND 5 ), -- Business Rule #2
	PRIMARY KEY (nome_profilo, competenza, nome_progetto),
	CONSTRAINT fk_skprof_profilo
		FOREIGN KEY (nome_profilo, nome_progetto)
			REFERENCES PROFILO (nome_profilo, nome_progetto)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_skprof_skill
		FOREIGN KEY (competenza)
			REFERENCES SKILL (competenza)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_skprof_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 16. PARTECIPANTE
CREATE TABLE PARTECIPANTE (
	email_utente  VARCHAR(100) NOT NULL,
	nome_progetto VARCHAR(100) NOT NULL,
	nome_profilo  VARCHAR(100) NOT NULL,
	stato         ENUM ('accettato','rifiutato','potenziale') DEFAULT 'potenziale',
	PRIMARY KEY (email_utente, nome_progetto, nome_profilo),
	CONSTRAINT fk_part_utente
		FOREIGN KEY (email_utente)
			REFERENCES UTENTE (email)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_part_progetto
		FOREIGN KEY (nome_progetto)
			REFERENCES PROGETTO (nome)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
	CONSTRAINT fk_part_profilo
		FOREIGN KEY (nome_profilo, nome_progetto)
			REFERENCES PROFILO (nome_profilo, nome_progetto)
			ON DELETE CASCADE
			ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ==================================================
-- STORED PROCEDURES (HELPER)
-- ==================================================

-- In questo blocco vengono definite le stored procedure di controllo ("helper") che verranno utilizzate dalle stored procedure principali ("main").
-- Sono inclusi qui solo i controlli che sono comuni a più stored procedure, per evitare duplicazione di codice. Laddove un controllo sia specifico
-- per una sola stored procedure, esso è incluso direttamente all'interno di quella stored procedure principale, e non qui.

-- SINTASSI GENERALE: sp_nome_procedura_check

-- L'unica eccezione sono le GENERICHE/UTILS sp_util_* che contengono controlli generici che possono essere utilizzati da più stored procedure principali e helper.
--     Le stored procedure che fanno SQLSTATE SIGNAL sono utilizzate all'interno delle stored procedure principali per lanciare un errore specifico.
--     Le stored procedure che restituiscono TRUE/FALSE sono utilizzate al livello di applicazione/php per fare controlli condizionali.

DELIMITER //

-- GENERICHE/UTILS: Insieme di controlli generici per tutte le stored procedure principali laddove necessario

/*
*  PROCEDURE: sp_util_is_utente_admin
*  PURPOSE: Verifica se l'utente è un admin, lanciando un errore se non lo è.
*
*  @param IN p_email - Email dell'utente da controllare
*
*  @throws 45000 - UTENTE NON ADMIN
*/
CREATE PROCEDURE sp_util_is_utente_admin(
	IN p_email VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM ADMIN
	               WHERE email_utente = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'UTENTE NON ADMIN';
	END IF;
END//

/*
*  PROCEDURE: sp_util_is_utente_creatore
*  PURPOSE: Verifica se l'utente è un creatore.
*
*  @param IN p_email - Email dell'utente da controllare
*
*  @throws 45000 - UTENTE NON CREATORE
*/
CREATE PROCEDURE sp_util_is_utente_creatore(
	IN p_email VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM CREATORE
	               WHERE email_utente = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'UTENTE NON CREATORE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_is_creatore_progetto_owner
*  PURPOSE: Verifica se l'utente è il creatore del progetto.
*
*  @param IN p_email - Email dell'utente da controllare
*  @param IN p_nome_progetto - Nome del progetto da controllare
*
*  @throws 45000 - UTENTE NON CREATORE DEL PROGETTO
*/
CREATE PROCEDURE sp_util_is_creatore_progetto_owner(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM PROGETTO
	               WHERE nome = p_nome_progetto
		             AND email_creatore = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'UTENTE NON CREATORE DEL PROGETTO';
	END IF;
END//

/*
*  PROCEDURE: sp_util_get_reward_by_code
*  PURPOSE: Recupera i dettagli di una reward dato il suo codice e il nome del progetto associato.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto associato alla reward
*  @param IN p_codice_reward - Codice della reward da recuperare
*/
CREATE PROCEDURE sp_util_get_reward_by_code(
	IN p_nome_progetto VARCHAR(100),
	IN p_codice_reward VARCHAR(50)
)
BEGIN
	START TRANSACTION;
	SELECT codice,
	       nome_progetto,
	       descrizione,
	       foto,
	       min_importo
	FROM REWARD
	WHERE nome_progetto = p_nome_progetto
	  AND codice = p_codice_reward;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_progetto_exists
*  PURPOSE: Verifica se il progetto esiste.
*
*  @param IN p_nome_progetto - Nome del progetto da controllare
*
*  @throws 45000 - PROGETTO NON ESISTENTE
*/
CREATE PROCEDURE sp_util_progetto_exists(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM PROGETTO
	               WHERE nome = p_nome_progetto) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO NON ESISTENTE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_is_progetto_software
*  PURPOSE: Verifica se il progetto è di tipo software.
*
*  @param IN p_nome_progetto - Nome del progetto da controllare
*
*  @throws 45000 - PROGETTO NON DI TIPO SOFTWARE
*/
CREATE PROCEDURE sp_util_is_progetto_software(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM PROGETTO_SOFTWARE
	               WHERE nome_progetto = p_nome_progetto) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO NON DI TIPO SOFTWARE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_is_progetto_hardware
*  PURPOSE: Verifica se il progetto è di tipo hardware.
*
*  @param IN p_nome_progetto - Nome del progetto da controllare
*
*  @throws 45000 - PROGETTO NON DI TIPO HARDWARE
*/
CREATE PROCEDURE sp_util_is_progetto_hardware(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM PROGETTO_HARDWARE
	               WHERE nome_progetto = p_nome_progetto) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO NON DI TIPO HARDWARE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_profilo_exists
*  PURPOSE: Verifica se il profilo di un progetto esiste.
*
*  @param IN p_nome_profilo - Nome del profilo da controllare
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il profilo
*
*  @throws 45000 - PROFILO NON ESISTENTE
*/
CREATE PROCEDURE sp_util_profilo_exists(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM PROFILO
	               WHERE nome_profilo = p_nome_profilo
		             AND nome_progetto = p_nome_progetto) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROFILO NON ESISTENTE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_skill_profilo_exists
*  PURPOSE: Verifica se la competenza richiesta dal profilo di un progetto esiste.
*
*  @param IN p_nome_profilo - Nome del profilo da controllare
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il profilo
*  @param IN p_competenza - Competenza richiesta dal profilo
*
*  @throws 45000 - COMPETENZA NON PRESENTE NEL PROFILO
*/
CREATE PROCEDURE sp_util_skill_profilo_exists(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_competenza VARCHAR(100)
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM SKILL_PROFILO
	               WHERE nome_profilo = p_nome_profilo
		             AND nome_progetto = p_nome_progetto
		             AND competenza = p_competenza) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'COMPETENZA NON PRESENTE NEL PROFILO';
	END IF;
END//

/*
*  PROCEDURE: sp_util_commento_exists
*  PURPOSE: Verifica se il commento esiste.
*
*  @param IN p_id - ID del commento da controllare
*
*  @throws 45000 - COMMENTO NON ESISTENTE
*/
CREATE PROCEDURE sp_util_commento_exists(
	IN p_id INT
)
BEGIN
	IF NOT EXISTS (SELECT 1
	               FROM COMMENTO
	               WHERE id = p_id) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'COMMENTO NON ESISTENTE';
	END IF;
END//

/*
*  PROCEDURE: sp_util_admin_exists
*  PURPOSE: Verifica se l'utente è un admin, selezionando TRUE se lo è, FALSE altrimenti.
*
*  @param IN p_email - Email dell'utente da controllare
*/
CREATE PROCEDURE sp_util_admin_exists(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	IF EXISTS (SELECT 1 FROM ADMIN WHERE email_utente = p_email) THEN
		SELECT TRUE AS is_admin;
	ELSE
		SELECT FALSE AS is_admin;
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_creatore_exists
*  PURPOSE: Verifica se l'utente è un creatore, selezionando TRUE se lo è, FALSE altrimenti.
*
*  @param IN p_email - Email dell'utente da controllare
*/
CREATE PROCEDURE sp_util_creatore_exists(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	IF EXISTS (SELECT 1 FROM CREATORE WHERE email_utente = p_email) THEN
		SELECT TRUE AS is_creatore;
	ELSE
		SELECT FALSE AS is_creatore;
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_progetto_owner_exists
*  PURPOSE: Verifica se l'utente è il creatore del progetto, selezionando TRUE se lo è, FALSE altrimenti.
*
*  @param IN p_email - Email dell'utente da controllare
*  @param IN p_nome_progetto - Nome del progetto da controllare
*/
CREATE PROCEDURE sp_util_progetto_owner_exists(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	IF EXISTS (SELECT 1 FROM PROGETTO WHERE email_creatore = p_email AND nome = p_nome_progetto) THEN
		SELECT TRUE AS is_owner;
	ELSE
		SELECT FALSE AS is_owner;
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_progetto_type
*  PURPOSE: Restituisce il tipo di progetto (software/hardware).
*
*  @param IN p_nome_progetto - Nome del progetto da controllare
*/
CREATE PROCEDURE sp_util_progetto_type(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	IF EXISTS (SELECT 1 FROM PROGETTO_SOFTWARE WHERE nome_progetto = p_nome_progetto) THEN
		SELECT 'SOFTWARE' AS tipo_progetto;
	ELSE
		SELECT 'HARDWARE' AS tipo_progetto;
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_get_admin_codice_sicurezza
*  PURPOSE: Visualizzazione del codice di sicurezza di un admin.
*  USED BY: ADMIN
*
*  @param IN p_email - Email dell'admin
*
*  @param OUT p_codice_sicurezza_out - Codice di sicurezza dell'admin
*/
CREATE PROCEDURE sp_util_get_admin_codice_sicurezza(
	IN p_email VARCHAR(100),
	OUT p_codice_sicurezza_out VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT codice_sicurezza
	INTO p_codice_sicurezza_out
	FROM ADMIN
	WHERE email_utente = p_email;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_utente_convert_creatore
*  PURPOSE: Conversione di un utente in creatore.
*  USED BY: UTENTE
*
*  @param IN p_email - Email dell'utente
*
*  @throws 45000 - EMAIL NON VALIDA
*  @throws 45000 - UTENTE GIA' CREATORE
*/
CREATE PROCEDURE sp_util_utente_convert_creatore(
	IN p_email VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che l'utente esista
	IF NOT EXISTS (SELECT 1 FROM UTENTE WHERE email = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'EMAIL NON VALIDA';
	END IF;

	-- Controllo che l'utente non sia già un creatore
	IF EXISTS (SELECT 1 FROM CREATORE WHERE email_utente = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'UTENTE GIA\' CREATORE';
	END IF;

	-- Conversione dell'utente in creatore
	INSERT INTO CREATORE (email_utente)
	VALUES (p_email);
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_get_creatore_affidabilita
*  PURPOSE: Visualizzazione dell'affidabilità di un creatore.
*  USED BY: CREATORE
*
*  @param IN p_email - Email del creatore
*/
CREATE PROCEDURE sp_util_get_creatore_affidabilita(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT affidabilita
	FROM CREATORE
	WHERE email_utente = p_email;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_get_creatore_nr_progetti
*  PURPOSE: Visualizzazione del numero di progetti creati da un creatore.
*  USED BY: CREATORE
*
*  @param IN p_email - Email del creatore
*/
CREATE PROCEDURE sp_util_get_creatore_nr_progetti(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT nr_progetti
	FROM CREATORE
	WHERE email_utente = p_email;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_finanziamento_progetto_utente_oggi_exists
*  PURPOSE: Verifica se l'utente ha finanziato il progetto oggi. Restituisce TRUE se ha finanziato, FALSE altrimenti.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*  @param IN p_nome_progetto - Nome del progetto
*/
CREATE PROCEDURE sp_util_finanziamento_progetto_utente_oggi_exists(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	IF EXISTS (SELECT 1
	           FROM FINANZIAMENTO
	           WHERE email_utente = p_email
		         AND nome_progetto = p_nome_progetto
		         AND data = CURRENT_DATE) THEN
		SELECT TRUE AS finanziato_oggi;
	ELSE
		SELECT FALSE AS finanziato_oggi;
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_util_progetto_componenti_costo
*  PURPOSE: Calcola il costo totale dei componenti di un progetto hardware.
*  USED BY: CREATORE
*
*  @param IN p_nome_progetto - Nome del progetto
*
*  @param OUT p_costo_totale - Costo totale dei componenti
*/
CREATE PROCEDURE sp_util_progetto_componenti_costo(
	IN p_nome_progetto VARCHAR(100),
	OUT p_costo_totale_out DECIMAL(10, 2)
)
BEGIN
	START TRANSACTION;
	SELECT SUM(prezzo * quantita)
	INTO p_costo_totale_out
	FROM COMPONENTE
	WHERE nome_progetto = p_nome_progetto;
	COMMIT;
END//

-- SKILL_PROFILO: sp_skill_profilo_check, USATO IN:
--  sp_skill_profilo_insert
--  sp_skill_profilo_delete
--  sp_skill_profilo_update

/*
*  PROCEDURE: sp_skill_profilo_check
*  PURPOSE: Controlla la validità di un profilo e di una competenza richiesta.
*  UTIL: sp_util_is_creatore_progetto_owner, sp_util_is_progetto_software, sp_util_profilo_exists, sp_util_skill_profilo_exists
*
*  @param IN p_nome_profilo - Nome del profilo da controllare
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il profilo
*  @param IN p_competenza - Competenza richiesta dal profilo
*  @param IN p_is_insert - Flag per distinguere tra insert, update e delete
*/
CREATE PROCEDURE sp_skill_profilo_check(
	IN p_nome_profilo VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_competenza VARCHAR(100),
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che il progetto sia creato dall'utente, e che sia di tipo software
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);
	CALL sp_util_is_progetto_software(p_nome_progetto);

	-- Controllo che il profilo del progetto esista
	CALL sp_util_profilo_exists(p_nome_profilo, p_nome_progetto);

	-- Controllo che il profilo abbia la competenza richiesta (solo per update e delete)
	IF NOT p_is_insert THEN
		CALL sp_util_skill_profilo_exists(p_nome_profilo, p_nome_progetto, p_competenza);
	END IF;
END//

-- PROFILO: sp_profilo_check, USATO IN:
--  sp_profilo_insert
--  sp_profilo_delete

/*
*  PROCEDURE: sp_profilo_check
*  PURPOSE: Controlla la validità di un profilo.
*  UTIL: sp_util_is_creatore_progetto_owner, sp_util_is_progetto_software, sp_util_profilo_exists
*
*  @param IN p_nome_profilo - Nome del profilo da controllare
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il profilo
*  @param IN p_is_insert - Flag per distinguere tra insert e delete
*/
CREATE PROCEDURE sp_profilo_check(
	IN p_nome_profilo VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che il progetto sia creato dall'utente, e che sia di tipo software
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);
	CALL sp_util_is_progetto_software(p_nome_progetto);

	-- Controllo che il profilo esista (solo per delete)
	IF NOT p_is_insert THEN
		CALL sp_util_profilo_exists(p_nome_profilo, p_nome_progetto);
	END IF;
END//

-- COMPONENTE: sp_componente_check USATO IN:
--  sp_componente_insert
--  sp_componente_delete
--  sp_componente_update

/*
*  PROCEDURE: sp_componente_check
*  PURPOSE: Controlla la validità di un componente.
*  UTIL: sp_util_is_creatore_progetto_owner, sp_util_is_progetto_hardware
*
*  @param IN p_nome_componente - Nome del componente da controllare
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il componente
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_is_insert - Flag per distinguere tra insert, update e delete
*
*  @throws 45000 - COMPONENTE NON ESISTENTE (+ altri throw specifici dalle sp_util utilizzate)
*/
CREATE PROCEDURE sp_componente_check(
	IN p_nome_componente VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che il progetto sia creato dall'utente, e che sia di tipo hardware
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);
	CALL sp_util_is_progetto_hardware(p_nome_progetto);

	-- Controllo che il componente esista (solo per update e delete)
	IF NOT p_is_insert THEN
		IF NOT EXISTS (SELECT 1
		               FROM COMPONENTE
		               WHERE nome_componente = p_nome_componente
			             AND nome_progetto = p_nome_progetto) THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'COMPONENTE NON ESISTENTE';
		END IF;
	END IF;
END//

-- PARTECIPANTE: sp_partecipante_check USATO IN:
--  sp_partecipante_creatore_update (sp_partecipante_creatore_check)
--  sp_partecipante_utente_insert (sp_partecipante_utente_check)

/*
*  PROCEDURE: sp_partecipante_check
*  PURPOSE: Controlla la validità di un partecipante. I controlli sono comuni a entrambe le stored procedure di seguito.
*  UTIL: sp_util_is_progetto_software, sp_util_skill_profilo_exists
*
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il partecipante
*  @param IN p_nome_profilo - Nome del profilo richiesto dal partecipante
*/
CREATE PROCEDURE sp_partecipante_check(
	IN p_nome_progetto VARCHAR(100),
	IN p_nome_profilo VARCHAR(100)
)
BEGIN
	-- Controllo che:
	-- 1. Il progetto sia di tipo software
	-- 2. Il profilo esista
	CALL sp_util_is_progetto_software(p_nome_progetto);
	CALL sp_util_profilo_exists(p_nome_profilo, p_nome_progetto);
END//

/*
*  PROCEDURE: sp_partecipante_creatore_check
*  PURPOSE: Controlla la validità di un partecipante. I controlli sono specifici per il creatore del progetto.
*  UTIL: sp_partecipante_check, sp_util_is_creatore_progetto_owner
*
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_email_candidato - Email dell'utente candidato al progetto
*  @param IN p_nome_progetto - Nome del progetto a cui il partecipante si è candidato
*  @param IN p_nome_profilo - Nome del profilo richiesto dal partecipante
*
*  @throws 45000 - CANDIDATURA NON ESISTENTE (+ altri throw specifici dalle sp_util utilizzate)
*/
CREATE PROCEDURE sp_partecipante_creatore_check(
	IN p_email_creatore VARCHAR(100),
	IN p_email_candidato VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_nome_profilo VARCHAR(100)
)
BEGIN
	-- Controllo comune a entrambe le stored procedure
	CALL sp_partecipante_check(p_nome_progetto, p_nome_profilo);
	-- Controllo che l'utente sia il creatore del progetto
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);

	-- Controllo che la candidatura esista
	IF NOT EXISTS (SELECT 1
	               FROM PARTECIPANTE
	               WHERE email_utente = p_email_candidato
		             AND nome_progetto = p_nome_progetto
		             AND nome_profilo = p_nome_profilo
		             AND stato = 'potenziale') THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'CANDIDATURA NON ESISTENTE';
	END IF;
END//

/*
*  PROCEDURE: sp_partecipante_utente_check
*  PURPOSE: Controlla la validità di un partecipante. I controlli sono specifici per l'utente che si candida al progetto software.
*
*  @param IN p_email - Email dell'utente che si candida al progetto
*  @param IN p_nome_progetto - Nome del progetto a cui il partecipante si candida
*  @param IN p_nome_profilo - Nome del profilo richiesto dal partecipante
*
*  @throws 45000 - UTENTE CREATORE DEL PROGETTO
*  @throws 45000 - CANDIDATURA INSERITA PRECEDENTEMENTE
*         (+ altri throw specifici da sp_partecipante_check)
*/
CREATE PROCEDURE sp_partecipante_utente_check(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_nome_profilo VARCHAR(100)
)
BEGIN
	-- Dichiarazione variabile per la competenza mancante/suo livello insufficiente (se esiste)
	DECLARE missing_skill VARCHAR(100) DEFAULT NULL;

	-- Controllo comune a entrambe le stored procedure
	CALL sp_partecipante_check(p_nome_progetto, p_nome_profilo);

	-- Controllo che l'utente NON sia il creatore del progetto
	IF EXISTS (SELECT 1
	           FROM PROGETTO
	           WHERE nome = p_nome_progetto
		         AND email_creatore = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'UTENTE CREATORE DEL PROGETTO';
	END IF;

	-- Controllo che l'utente non abbia già una candidatura per quel profilo del progetto
	IF EXISTS (SELECT 1
	           FROM PARTECIPANTE
	           WHERE email_utente = p_email
		         AND nome_progetto = p_nome_progetto
		         AND nome_profilo = p_nome_profilo) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'CANDIDATURA INSERITA PRECEDENTEMENTE';
	END IF;

	-- Per ogni competenza richiesta dal profilo, controlla che il candidato abbia una entry in SKILL_CURRICULUM
	-- con un livello_effettivo maggiore o uguale al livello_richiesto.
	SELECT sp.competenza
	INTO missing_skill
	FROM SKILL_PROFILO sp
		     LEFT JOIN SKILL_CURRICULUM sc
		               ON sp.competenza = sc.competenza AND sc.email_utente = p_email
	WHERE sp.nome_profilo = p_nome_profilo
	  AND sp.nome_progetto = p_nome_progetto
	  AND (sc.livello_effettivo IS NULL OR sc.livello_effettivo < sp.livello_richiesto)
	LIMIT 1;

	-- Se almeno una competenza problematica viene trovata, lancia un errore.
	IF missing_skill IS NOT NULL THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'COMPETENZA MANCANTE O LIVELLO INSUFFICIENTE';
	END IF;
END//

-- COMMENTO: sp_commento_check e sp_commento_risposta_check USATI IN:
--  sp_commento_insert (sp_commento_check)
--  sp_commento_delete (sp_commento_check)
--  sp_commento_risposta_insert (sp_commento_risposta_check)
--  sp_commento_risposta_delete (sp_commento_risposta_check)

/*
*  PROCEDURE: sp_commento_check
*  PURPOSE: Controlla la validità di un commento.
*  UTIL: sp_util_progetto_exists, sp_util_commento_exists
*
*  @param IN p_id - ID del commento da controllare
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il commento
*  @param IN p_email_autore - Email dell'autore del commento
*  @param IN p_is_insert - Flag per distinguere tra insert e delete
*
*  @throws 45000 - NON SEI AUTORIZZATO A CANCELLARE QUESTO COMMENTO (+ altri throw specifici dalle sp_util utilizzate)
*/
CREATE PROCEDURE sp_commento_check(
	IN p_id INT,
	IN p_nome_progetto VARCHAR(100),
	IN p_email_autore VARCHAR(100),
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che il progetto esista
	CALL sp_util_progetto_exists(p_nome_progetto);

	-- Controllo che il commento esista (solo per delete)
	IF NOT p_is_insert THEN
		CALL sp_util_commento_exists(p_id);
		-- Controllo che l'utente sia l'autore del commento, OPPURE un admin
		IF NOT (
			EXISTS (SELECT 1 FROM COMMENTO WHERE id = p_id AND email_utente = p_email_autore)
				OR
			EXISTS (SELECT 1 FROM ADMIN WHERE email_utente = p_email_autore)
			) THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'NON SEI AUTORIZZATO A CANCELLARE QUESTO COMMENTO';
		END IF;
	END IF;
END//

/*
*  PROCEDURE: sp_commento_risposta_check
*  PURPOSE: Controlla la validità di una risposta a un commento.
*  UTIL: sp_util_progetto_exists, sp_util_commento_exists, sp_util_is_creatore_progetto_owner
*
*  @param IN p_commento_id - ID del commento a cui si vuole rispondere/cancellare la risposta
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene il commento
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_is_insert - Flag per distinguere tra insert e delete
*
*  @throws 45000 - COMMENTO CONTIENE RISPOSTA
*  @throws 45000 - UTENTE NON CREATORE O ADMIN (CANCELLAZIONE RISPOSTA)
*  @throws 45000 - COMMENTO NON CONTIENE RISPOSTA
*          (+ altri throw specifici dalle sp_util utilizzate)
*/
CREATE PROCEDURE sp_commento_risposta_check(
	IN p_commento_id INT,
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che il progetto e il commento esistano
	CALL sp_util_progetto_exists(p_nome_progetto);
	CALL sp_util_commento_exists(p_commento_id);

	-- Se l'intento è di inserire una risposta...
	IF p_is_insert THEN
		-- Controllo che l'utente sia il creatore del progetto
		CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);
		-- Controllo che il commento NON ABBIA una risposta
		IF EXISTS (SELECT 1 FROM COMMENTO WHERE id = p_commento_id AND risposta IS NOT NULL) THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'COMMENTO CONTIENE RISPOSTA';
		END IF;
	ELSE
		-- Altrimenti si intende cancellare una risposta...
		IF NOT ( -- L'utente deve essere il creatore del progetto o un admin
			EXISTS (SELECT 1 FROM PROGETTO WHERE nome = p_nome_progetto AND email_creatore = p_email_creatore)
				OR
			EXISTS (SELECT 1 FROM ADMIN WHERE email_utente = p_email_creatore)
			) THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'UTENTE NON CREATORE O ADMIN (CANCELLAZIONE RISPOSTA)';
		END IF;

		-- Controllo che il commento ABBIA una risposta da cancellare
		IF EXISTS (SELECT 1 FROM COMMENTO WHERE id = p_commento_id AND risposta IS NULL) THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'COMMENTO NON CONTIENE RISPOSTA';
		END IF;
	END IF;
END//

-- FOTO: sp_foto_check USATO IN:
--  sp_foto_insert
--  sp_foto_delete

/*
*  PROCEDURE: sp_foto_check
*  PURPOSE: Controlla la validità di una foto.
*  UTIL: sp_util_is_creatore_progetto_owner
*
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene la foto
*  @param IN p_email_creatore - Email dell'utente creatore del progetto
*  @param IN p_foto_id - ID della foto da controllare
*  @param IN p_is_insert - Flag per distinguere tra insert e delete
*
*  @throws 45000 - FOTO NON ESISTENTE (+ altri throw specifici da sp_util_is_creatore_progetto_owner)
*/
CREATE PROCEDURE sp_foto_check(
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_foto_id INT,
	IN p_is_insert BOOLEAN
)
BEGIN
	-- Controllo che l'utente sia il creatore del progetto
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);

	-- Controllo che la foto esista (solo per delete)
	IF NOT p_is_insert AND NOT EXISTS (SELECT 1
	                                   FROM FOTO
	                                   WHERE nome_progetto = p_nome_progetto
		                                 AND id = p_foto_id) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'FOTO NON ESISTENTE';
	END IF;
END//

DELIMITER ;

-- ==================================================
-- STORED PROCEDURES (MAIN)
-- ==================================================

-- In questo blocco vengono definite le stored procedure principali ("main"). Implementano le funzionalità richieste dal sistema, con qualche aggiunta.
-- Vengono divise in base alla tabella di riferimento, con la seguente sintassi generale...

-- NOME_TABELLA:
--  sp_nome_tabella_{insert|delete|update|select} o altre azioni specifiche

-- Al di sopra della definizione di ogni stored procedure principale, si documenta la funzione di esse e che tipo di utente può utilizzarle.
-- L'interno di ogni stored procedure è diviso in due parti principali:
--  1. Controllo dei parametri in input (il controllo assistito dalle stored procedure "helper")
--  2. Operazioni sul database relative

DELIMITER //

-- UTENTE:
--  sp_utente_register
--  sp_utente_login
--  sp_utente_select

/*
*  PROCEDURE: sp_utente_register
*  PURPOSE: Registrazione di un utente con o senza ruolo di creatore, e/o admin.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*  @param IN p_password - Password dell'utente
*  @param IN p_nickname - Nickname dell'utente
*  @param IN p_nome - Nome dell'utente
*  @param IN p_cognome - Cognome dell'utente
*  @param IN p_anno_nascita - Anno di nascita dell'utente
*  @param IN p_luogo_nascita - Luogo di nascita dell'utente
*  @param IN p_is_creatore - Flag che indica se l'utente è un creatore
*/
CREATE PROCEDURE sp_utente_register(
	IN p_email VARCHAR(100),
	IN p_password VARCHAR(255),
	IN p_nickname VARCHAR(50),
	IN p_nome VARCHAR(50),
	IN p_cognome VARCHAR(50),
	IN p_anno_nascita INT,
	IN p_luogo_nascita VARCHAR(50),
	IN p_is_creatore BOOLEAN, -- Definito a livello di interfaccia
	IN p_is_admin BOOLEAN, -- Definito a livello di interfaccia
	IN p_codice_sicurezza VARCHAR(100) -- Definito a livello di interfaccia
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION -- Gestione delle eccezioni, rollback e propagazione dell'errore
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION; -- Uso di transazione per garantire l'integrità dei dati
	INSERT INTO UTENTE (email, password, nickname, nome, cognome, anno_nascita, luogo_nascita)
	VALUES (p_email, p_password, p_nickname, p_nome, p_cognome, p_anno_nascita, p_luogo_nascita);

	IF p_is_creatore THEN
		INSERT INTO CREATORE (email_utente)
		VALUES (p_email);
	END IF;

	IF p_is_admin THEN
		INSERT INTO ADMIN (email_utente, codice_sicurezza)
		VALUES (p_email, p_codice_sicurezza);
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_utente_login
*  PURPOSE: Login di un utente, restituisce i dati dell'utente se esiste.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*
*  @param OUT p_nickname_out - Nickname dell'utente
*  @param OUT p_email_out - Email dell'utente
*  @param OUT p_password_hash_out - Hash della password dell'utente
*/
CREATE PROCEDURE sp_utente_login(
	IN p_email VARCHAR(100),
	OUT p_nickname_out VARCHAR(50),
	OUT p_email_out VARCHAR(100),
	OUT p_password_hash_out VARCHAR(255)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- (ALL) Controllo che l'utente esista
	IF NOT EXISTS (SELECT 1 FROM UTENTE WHERE email = p_email) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'EMAIL NON VALIDA';
	END IF;

	-- Se il controllo passa, restituisco i dati dell'utente
	SELECT nickname, email, password
	INTO p_nickname_out, p_email_out, p_password_hash_out
	FROM UTENTE
	WHERE email = p_email;
	COMMIT;
END//

/*
*  PROCEDURE: sp_utente_select
*  PURPOSE: Visualizzazione dei dati di un utente (home.php).
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*/
CREATE PROCEDURE sp_utente_select(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT nome, cognome, nickname, anno_nascita, luogo_nascita
	FROM UTENTE
	WHERE email = p_email;
	COMMIT;
END//

-- SKILL_CURRICULUM:
--  sp_skill_curriculum_insert
--  sp_skill_curriculum_selectAll
--  sp_skill_curriculum_selectDiff

/*
*  PROCEDURE: sp_skill_curriculum_insert
*  PURPOSE: Inserimento di una skill in un curriculum utente.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*  @param IN p_competenza - Competenza da inserire nel curriculum
*  @param IN p_livello - Livello della competenza da inserire (da 0 a 5)
*/
CREATE PROCEDURE sp_skill_curriculum_insert(
	IN p_email VARCHAR(100),
	IN p_competenza VARCHAR(100),
	IN p_livello TINYINT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	INSERT INTO SKILL_CURRICULUM (email_utente, competenza, livello_effettivo)
	VALUES (p_email, p_competenza, p_livello);
	COMMIT;
END//

/*
*  PROCEDURE: sp_skill_curriculum_selectAll
*  PURPOSE: Visualizzazione di tutte le skill di un utente.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*/
CREATE PROCEDURE sp_skill_curriculum_selectAll(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT competenza, livello_effettivo
	FROM SKILL_CURRICULUM
	WHERE email_utente = p_email;
	COMMIT;
END//

/*
*  PROCEDURE: sp_skill_curriculum_selectDiff
*  PURPOSE: Visualizzazione delle skill globali di cui un utente non dispone/non ha inserito nel proprio curriculum.
*  USED BY: ALL
*
*  @param IN p_email - Email dell'utente
*/
CREATE PROCEDURE sp_skill_curriculum_selectDiff(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT competenza
	FROM SKILL
	WHERE competenza NOT IN (SELECT competenza
	                         FROM SKILL_CURRICULUM
	                         WHERE email_utente = p_email);
	COMMIT;
END//

-- PROGETTO:
--  sp_progetto_select
--  sp_progetto_selectAll
--  sp_progetto_selectByCreatore
--  sp_progetto_insert
--  sp_progetto_descrizione_update
--  sp_progetto_budget_update

/*
*  PROCEDURE: sp_progetto_select
*  PURPOSE: Visualizzazione di un progetto specifico.
*  USED BY: ALL
*
*  @param IN p_nome - Nome del progetto
*/
CREATE PROCEDURE sp_progetto_select(
	IN p_nome VARCHAR(100)
)
BEGIN
	SELECT *
	FROM PROGETTO
	WHERE nome = p_nome;
END//

/*
*  PROCEDURE: sp_progetto_selectAll
*  PURPOSE: Visualizzazione di tutti i progetti disponibili.
*  USED BY: ALL
*/
CREATE PROCEDURE sp_progetto_selectAll()
BEGIN
	SELECT * FROM PROGETTO;
END//

/*
*  PROCEDURE: sp_progetto_selectByCreatore
*  PURPOSE: Visualizzazione di tutti i progetti di un creatore.
*  USED BY: CREATORE
*
*  @param IN p_email - Email del creatore
*/
CREATE PROCEDURE sp_progetto_selectByCreatore(
	IN p_email VARCHAR(100)
)
BEGIN
	SELECT *
	FROM PROGETTO
	WHERE email_creatore = p_email;
END//

/*
*  PROCEDURE: sp_progetto_insert
*  PURPOSE: Inserimento di un nuovo progetto da parte di un creatore.
*  USED BY: CREATORE
*  NOTE: Il campo CREATORE.nr_progetti_creati viene incrementato tramite trigger (trg_incrementa_progetti_creati) dopo l'inserimento del progetto.
*
*  @param IN p_nome - Nome del progetto
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_descrizione - Descrizione del progetto
*  @param IN p_budget - Budget del progetto
*  @param IN p_data_limite - Data limite per il progetto
*  @param IN p_tipo - Tipo di progetto (software o hardware)
*/
CREATE PROCEDURE sp_progetto_insert(
	IN p_nome VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_descrizione TEXT,
	IN p_budget DECIMAL(10, 2),
	IN p_data_limite DATE,
	IN p_tipo ENUM ('software','hardware') -- Tipo di progetto, definito a livello di interfaccia (checkbox)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che l'utente sia un creatore
	CALL sp_util_is_utente_creatore(p_email_creatore);

	-- Controllo che la data_limite sia futura alla data attuale
	IF p_data_limite <= CURRENT_DATE THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'DATA LIMITE DEVE ESSERE FUTURA';
	END IF;

	INSERT INTO PROGETTO (nome, email_creatore, descrizione, budget, data_limite)
	VALUES (p_nome, p_email_creatore, p_descrizione, p_budget, p_data_limite);

	-- Insert in tabella specifica in base al tipo di progetto
	IF p_tipo = 'software' THEN
		INSERT INTO PROGETTO_SOFTWARE (nome_progetto)
		VALUES (p_nome);
	ELSEIF p_tipo = 'hardware' THEN
		INSERT INTO PROGETTO_HARDWARE (nome_progetto)
		VALUES (p_nome);
	END IF;
	COMMIT;
END//

/*
*  PROCEDURE: sp_progetto_descrizione_update
*  PURPOSE: Aggiornamento della descrizione di un progetto.
*  USED BY: CREATORE
*
*  @param IN p_nome - Nome del progetto
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_descrizione - Nuova descrizione del progetto
*/
CREATE PROCEDURE sp_progetto_descrizione_update(
	IN p_nome VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_descrizione TEXT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	-- 1. L'utente sia il creatore del progetto
	-- 2. La descrizione non sia nulla
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome);

	IF p_descrizione IS NULL THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'DESCRIZIONE NON VALIDA';
	END IF;

	-- Se i controlli passano, aggiorno la descrizione
	UPDATE PROGETTO
	SET descrizione = p_descrizione
	WHERE nome = p_nome;
	COMMIT;
END//

/*
*  PROCEDURE: sp_progetto_budget_update
*  PURPOSE: Aggiornamento del budget di un progetto.
*  USED BY: CREATORE
*
*  @param IN p_nome - Nome del progetto
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_budget - Nuovo budget del progetto
*/
CREATE PROCEDURE sp_progetto_budget_update(
	IN p_nome VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_budget DECIMAL(10, 2)
)
BEGIN
	DECLARE current_budget DECIMAL(10, 2);
	DECLARE current_state VARCHAR(20);
	DECLARE tot_finanziamento DECIMAL(10, 2);
	DECLARE tot_componenti DECIMAL(10, 2);

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che l'utente sia il creatore del progetto
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome);

	-- Recupero il budget corrente e lo stato del progetto
	SELECT budget, stato
	INTO current_budget, current_state
	FROM PROGETTO
	WHERE nome = p_nome;

	-- Se il progetto non è aperto, restituisce un errore (non è possibile modificare un progetto chiuso)
	IF current_state != 'aperto' THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'IMPOSSIBILE MODIFICARE BUDGET DI PROGETTO CHIUSO';
	END IF;

	-- Mi assicuro che il nuovo budget sia maggiore di 0
	IF p_budget <= 0 THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'BUDGET PROGETTO DEVE ESSERE > 0';
	END IF;

	-- Per i progetti hardware, verifico che il nuovo budget sia almeno uguale al costo totale dei componenti
	IF EXISTS(SELECT 1 FROM PROGETTO_HARDWARE WHERE nome_progetto = p_nome) THEN
		SELECT IFNULL(SUM(prezzo * quantita), 0)
		INTO tot_componenti
		FROM COMPONENTE
		WHERE nome_progetto = p_nome;

		IF p_budget < tot_componenti THEN
			SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'BUDGET PROGETTO DEVE ESSERE >= SOMMA COSTO COMPONENTI';
		END IF;
	END IF;

	-- Se il budget viene modificato, controllo il totale dei finanziamenti
	IF p_budget != current_budget THEN
		SELECT IFNULL(SUM(importo), 0)
		INTO tot_finanziamento
		FROM FINANZIAMENTO
		WHERE nome_progetto = p_nome;

		-- Se la somma finanziamenti è >= al nuovo budget imposto lo stato a 'chiuso'
		IF tot_finanziamento >= p_budget THEN
			UPDATE PROGETTO
			SET stato = 'chiuso'
			WHERE nome = p_nome;
			COMMIT;
		END IF;
	END IF;

	-- Se tutti i controlli passano, aggiorno il budget del progetto
	UPDATE PROGETTO
	SET budget = p_budget
	WHERE nome = p_nome;
	COMMIT;
END//

-- FINANZIAMENTO:
--  sp_finanziamento_insert
--  sp_finanziamento_selectSumByProgetto
--  sp_finanziamento_selectAllByProgetto
--  sp_finanziamento_selectAllByUtente

/*
*  PROCEDURE: sp_finanziamento_insert
*  PURPOSE: Finanziamento di un progetto da parte di un utente.
*  USED BY: ALL
*  NOTE:
*   - Il progetto deve essere aperto
*   - Il codice della reward è scelto dall'utente al livello di interfaccia al momento del finanziamento, in base all'importo scelto
*   - Anche il creatore può finanziare il proprio progetto
*
*  @param IN p_email - Email dell'utente
*  @param IN p_nome_progetto - Nome del progetto
*  @param IN p_codice_reward - Codice del reward scelto dall'utente
*  @param IN p_importo - Importo del finanziamento
*/
CREATE PROCEDURE sp_finanziamento_insert(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_codice_reward VARCHAR(50), -- Scelto al livello di interfaccia
	IN p_importo DECIMAL(10, 2)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che il progetto sia aperto
	IF NOT EXISTS (SELECT 1
	               FROM PROGETTO
	               WHERE nome = p_nome_progetto
		             AND stato = 'aperto') THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO CHIUSO O NON ESISTENTE';
	END IF;

	-- Se il controllo passa, inserisco il finanziamento
	INSERT INTO FINANZIAMENTO (email_utente, nome_progetto, codice_reward, importo)
	VALUES (p_email, p_nome_progetto, p_codice_reward, p_importo);
	COMMIT;
END//

/*
*  PROCEDURE: sp_finanziamento_selectSumByProgetto
*  PURPOSE: Restituisce la somma degli importi dei finanziamenti ricevuti da un progetto.
*
*  @param IN p_nome_progetto - Nome del progetto da cui si vuole ottenere il totale dei finanziamenti
*/
CREATE PROCEDURE sp_finanziamento_selectSumByProgetto(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT SUM(importo) AS totale_finanziamenti
	FROM FINANZIAMENTO
	WHERE nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_finanziamento_selectAllByProgetto
*  PURPOSE: Restituisce tutti i finanziamenti ricevuti dai progetti creati da un utente specifico.
*  USED BY: CREATORE
*
*  @param IN p_email_creatore - Email del creatore di cui si vogliono ottenere i finanziamenti ricevuti
*/
CREATE PROCEDURE sp_finanziamento_selectAllByProgetto(
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT F.data,
	       F.email_utente,
	       U.nickname    AS finanziatore_nickname,
	       F.nome_progetto,
	       F.codice_reward,
	       F.importo,
	       R.descrizione AS reward_descrizione,
	       R.foto        AS reward_foto,
	       P.budget      AS progetto_budget,
	       P.stato       AS progetto_stato
	FROM FINANZIAMENTO F
		     JOIN PROGETTO P ON F.nome_progetto = P.nome
		     JOIN UTENTE U ON F.email_utente = U.email
		     JOIN REWARD R ON F.codice_reward = R.codice AND F.nome_progetto = R.nome_progetto
	WHERE P.email_creatore = p_email_creatore
	ORDER BY F.data DESC;
	COMMIT;
END//

/*
*  PROCEDURE: sp_finanziamento_selectAllByUtente
*  PURPOSE: Restituisce tutti i finanziamenti effettuati da un utente specifico con dettagli completi su reward e progetto.
*
*  @param IN p_email - Email dell'utente da cui si vuole ottenere la lista dei finanziamenti
*/
CREATE PROCEDURE sp_finanziamento_selectAllByUtente(
	IN p_email VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT F.data,
	       F.email_utente,
	       F.nome_progetto,
	       F.codice_reward,
	       F.importo,
	       R.descrizione AS reward_descrizione,
	       R.foto        AS reward_foto,
	       R.min_importo AS reward_min_importo,
	       P.email_creatore,
	       P.stato       AS progetto_stato,
	       P.budget      AS progetto_budget,
	       P.data_limite AS progetto_data_limite
	FROM FINANZIAMENTO F
		     JOIN PROGETTO P ON F.nome_progetto = P.nome
		     JOIN REWARD R ON F.codice_reward = R.codice AND F.nome_progetto = R.nome_progetto
	WHERE F.email_utente = p_email
	ORDER BY F.data DESC;
	COMMIT;
END//

-- COMMENTO:
--  sp_commento_insert
--  sp_commento_delete
--  sp_commento_selectAll
--  sp_commento_risposta_insert
--  sp_commento_risposta_delete

/*
*  PROCEDURE: sp_commento_insert
*  PURPOSE: Inserimento di un commento a un progetto esistente.
*  USED BY: ALL
*
*  @param IN p_email_autore - Email dell'autore del commento
*  @param IN p_nome_progetto - Nome del progetto
*  @param IN p_testo - Testo del commento
*/
CREATE PROCEDURE sp_commento_insert(
	IN p_email_autore VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_testo TEXT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che esista il progetto
	CALL sp_commento_check(NULL, p_nome_progetto, p_email_autore, TRUE);

	-- Se il controllo passa, inserisco il commento
	INSERT INTO COMMENTO (email_utente, nome_progetto, testo)
	VALUES (p_email_autore, p_nome_progetto, p_testo);
	COMMIT;
END//

/*
*  PROCEDURE: sp_commento_delete
*  PURPOSE: Cancellazione di un commento a un progetto esistente.
*  USED BY: ALL
*  NOTE: L'utente può cancellare solo i propri commenti, mentre un admin può cancellare qualsiasi commento.
*
*  @param IN p_id - ID del commento da cancellare
*  @param IN p_email - Email dell'utente (autore del commento o admin)
*  @param IN p_nome_progetto - Nome del progetto del quale fa parte il commento
*/
CREATE PROCEDURE sp_commento_delete(
	IN p_id INT,
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Esista il progetto
	--  2. Esista il commento
	--  3. L'utente sia l'autore del commento, OPPURE un admin
	CALL sp_commento_check(p_id, p_nome_progetto, p_email, FALSE);

	-- Se i controlli passano, cancello il commento
	DELETE
	FROM COMMENTO
	WHERE id = p_id;
	COMMIT;
END//

/*
*  PROCEDURE: sp_commento_selectAll
*  PURPOSE: Visualizzazione di tutti i commenti di un progetto.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto
*/
CREATE PROCEDURE sp_commento_selectAll(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	SELECT C.id, C.email_utente, U.nickname, C.testo, C.risposta, C.data
	FROM COMMENTO C
		     JOIN UTENTE U ON U.email = C.email_utente
	WHERE nome_progetto = p_nome_progetto;
END//

/*
*  PROCEDURE: sp_commento_risposta_insert
*  PURPOSE: Inserimento di una risposta a un commento esistente.
*  USED BY: CREATORE
*
*  @param IN p_commento_id - ID del commento a cui rispondere
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_nome_progetto - Nome del progetto del quale fa parte il commento
*  @param IN p_risposta - Testo della risposta
*/
CREATE PROCEDURE sp_commento_risposta_insert(
	IN p_commento_id INT,
	IN p_email_creatore VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_risposta TEXT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Esista il progetto
	--  2. Esista il commento
	--  3. L'utente sia il creatore del progetto
	--  4. Il commento non abbia già una risposta
	CALL sp_commento_risposta_check(p_commento_id, p_nome_progetto, p_email_creatore, TRUE);

	-- Se i controlli passano, inserisco la risposta
	UPDATE COMMENTO
	SET risposta = p_risposta
	WHERE id = p_commento_id;
	COMMIT;
END//

/*
*  PROCEDURE: sp_commento_risposta_delete
*  PURPOSE: Cancellazione di una risposta a un commento esistente.
*  USED BY: CREATORE
*  NOTE: Sia il creatore che un admin possono cancellare le risposte ai commenti del progetto suo (creatore).
*
*  @param IN p_commento_id - ID del commento con risposta da cancellare
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_nome_progetto - Nome del progetto del quale fa parte il commento
*/
CREATE PROCEDURE sp_commento_risposta_delete(
	IN p_commento_id INT,
	IN p_email_creatore VARCHAR(100),
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Esista il progetto
	--  2. Esista il commento
	--  3. L'utente sia il creatore del progetto, OPPURE un admin
	--  4. Il commento abbia una risposta
	CALL sp_commento_risposta_check(p_commento_id, p_nome_progetto, p_email_creatore, FALSE);

	-- Se i controlli passano, cancello la risposta
	UPDATE COMMENTO
	SET risposta = NULL
	WHERE id = p_commento_id;
	COMMIT;
END//

-- PARTECIPANTE:
--  sp_partecipante_utente_insert
--  sp_partecipante_creatore_update

/*
*  PROCEDURE: sp_partecipante_utente_insert
*  PURPOSE: Inserimento di una candidatura a un progetto software da parte di un utente.
*  USED BY: ALL
*  NOTE:
*   - L'utente non può candidarsi a un progetto di cui è creatore, e non può candidarsi più di una volta per la stessa competenza.
*   - Utenti che non dispongono della competenza richiesta non possono candidarsi.
*   - Utenti che non dispongono del livello richiesto per la competenza non possono candidarsi e vengono automaticamente rifiutati
*     senza mai essere inseriti nella tabella PARTECIPANTE (vedi trigger trg_rifiuta_candidatura_livello_effettivo_insufficiente).
*
*  @param IN p_email - Email dell'utente che si candida
*  @param IN p_nome_progetto - Nome del progetto interessato
*  @param IN p_nome_profilo - Nome del profilo richiesto per il progetto
*/
CREATE PROCEDURE sp_partecipante_utente_insert(
	IN p_email VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_nome_profilo VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Il progetto sia di tipo software
	--  2. Il profilo per quel progetto esista
	--  3. L'utente non sia il creatore del progetto
	--  4. L'utente non abbia già una candidatura per quel profilo del progetto
	--  5. L'utente, per ogni competenza richiesta, abbia il livello richiesto
	CALL sp_partecipante_utente_check(p_email, p_nome_progetto, p_nome_profilo);

	-- Se i controlli passano, inserisco la candidatura
	INSERT INTO PARTECIPANTE (email_utente, nome_progetto, nome_profilo)
	VALUES (p_email, p_nome_progetto, p_nome_profilo);
	COMMIT;
END//

/*
*  PROCEDURE: sp_partecipante_creatore_update
*  PURPOSE: Accettazione o rifiuto di una candidatura a un progetto software da parte del creatore.
*  USED BY: CREATORE
*  NOTE: Solo il creatore, in quanto tale, può accettare o rifiutare una candidatura.
*
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_email_candidato - Email del candidato
*  @param IN p_nome_progetto - Nome del progetto interessato
*  @param IN p_nome_profilo - Nome del profilo richiesto per il progetto
*  @param IN p_nuovo_stato - Nuovo stato della candidatura (accettato o rifiutato)
*/
CREATE PROCEDURE sp_partecipante_creatore_update(
	IN p_email_creatore VARCHAR(100),
	IN p_email_candidato VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_nome_profilo VARCHAR(100),
	IN p_nuovo_stato ENUM ('accettato','rifiutato')
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Il progetto sia di tipo software
	--  2. Il profilo per quel progetto esista
	--  3. L'utente sia il creatore del progetto
	--  4. La candidatura esista
	CALL sp_partecipante_creatore_check(p_email_creatore, p_email_candidato, p_nome_progetto, p_nome_profilo);

	-- Se i controlli passano, aggiorno lo stato della candidatura
	UPDATE PARTECIPANTE
	SET stato = p_nuovo_stato
	WHERE email_utente = p_email_candidato
	  AND nome_progetto = p_nome_progetto
	  AND nome_profilo = p_nome_profilo;
	COMMIT;
END//

-- SKILL:
--  sp_skill_insert
--  sp_skill_selectAll

/*
*  PROCEDURE: sp_skill_insert
*  PURPOSE: Inserimento di una competenza nella lista delle competenze disponibili.
*  USED BY: ADMIN
*
*  @param IN p_competenza - Competenza/skill da inserire
*  @param IN p_email - Email dell'utente admin che esegue la procedura
*/
CREATE PROCEDURE sp_skill_insert(
	IN p_competenza VARCHAR(100),
	IN p_email VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che l'admin sia l'utente che esegue la procedura
	CALL sp_util_is_utente_admin(p_email);

	-- Se il controllo passa, inserisco la competenza
	INSERT INTO SKILL (competenza)
	VALUES (p_competenza);
	COMMIT;
END//

/*
*  PROCEDURE: sp_skill_selectAll
*  PURPOSE: Visualizzazione di tutte le competenze disponibili nel sistema.
*  USED BY: ALL
*/
CREATE PROCEDURE sp_skill_selectAll()
BEGIN
	START TRANSACTION;
	SELECT competenza
	FROM SKILL;
	COMMIT;
END//

-- REWARD:
--  sp_reward_insert
--  sp_reward_selectAllByProgetto
--  sp_reward_selectAllByFinanziamentoImporto

/*
*  PROCEDURE: sp_reward_insert
*  PURPOSE: Inserimento di una reward per un progetto.
*  USED BY: CREATORE
*  NOTE:
*    - p_codice è un codice univoco per la reward, definito dal creatore al momento dell'inserimento
*    - p_min_importo è l'importo minimo per il quale un finanziatore è eleggibile alla reward quando effettua un finanziamento
*
*  @param IN p_codice - Codice della reward definito dal creatore
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene la reward
*  @param IN p_email_creatore - Email del creatore del progetto che inserisce la reward
*  @param IN p_descrizione - Descrizione della reward
*  @param IN p_foto - Foto della reward
*  @param IN p_min_importo - Importo minimo per essere eleggibili alla reward
*/
CREATE PROCEDURE sp_reward_insert(
	IN p_codice VARCHAR(50),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_descrizione TEXT,
	IN p_foto MEDIUMBLOB,
	IN p_min_importo DECIMAL(10, 2)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. Il progetto esista
	--  2. L'utente sia il creatore del progetto
	CALL sp_util_progetto_exists(p_nome_progetto);
	CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);

	-- Se i controlli passano, inserisco la reward
	INSERT INTO REWARD (codice, nome_progetto, descrizione, foto, min_importo)
	VALUES (p_codice, p_nome_progetto, p_descrizione, p_foto, p_min_importo);
	COMMIT;
END//

/*
*  PROCEDURE: sp_reward_selectAllByProgetto
*  PURPOSE: Visualizzazione di tutte le reward di un progetto.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto
*/
CREATE PROCEDURE sp_reward_selectAllByProgetto(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	-- Controllo che il progetto esista
	CALL sp_util_progetto_exists(p_nome_progetto);

	-- Se il controllo passa, restituisco le reward
	SELECT codice, descrizione, foto, min_importo
	FROM REWARD
	WHERE nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_reward_selectAllByFinanziamentoImporto
*  PURPOSE: Visualizzazione di tutte le reward disponibili per un progetto in base all'importo donato.
*           Restituisce le reward per le quali l'importo donato è maggiore o uguale al valore minimo richiesto (min_importo).
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto
*  @param IN p_importo - Importo del finanziamento effettuato dall'utente
*/
CREATE PROCEDURE sp_reward_selectAllByFinanziamentoImporto(
	IN p_nome_progetto VARCHAR(100),
	IN p_importo DECIMAL(10, 2)
)
BEGIN
	START TRANSACTION;
	SELECT codice, descrizione, foto, min_importo
	FROM REWARD
	WHERE nome_progetto = p_nome_progetto
	  AND min_importo <= p_importo;
	COMMIT;
END//

-- COMPONENTE:
--  sp_componente_insert
--  sp_componente_delete
--  sp_componente_update
--  sp_componente_selectAllByProgetto

/*
*  PROCEDURE: sp_componente_insert
*  PURPOSE: Inserimento di un componente per un progetto hardware.
*  USED BY: CREATORE
*  NOTE:
*    - Il prezzo e la quantità del componente vengono definiti dal creatore al momento dell'inserimento.
*    - Il prezzo e la quantità del componente aggiorneranno il budget del progetto se (prezzo * quantità) > budget attuale del progetto.
*       - Il budget aumenta della differenza tra il prezzo totale dei componenti e il budget (vedi trigger trg_update_budget_componente_insert).
*       - Il creatore è notificato a livello di interfaccia del cambio di budget.
*
*  @param IN p_nome_componente - Nome del componente da inserire per il progetto hardware
*  @param IN p_nome_progetto - Nome del progetto hardware
*  @param IN p_descrizione - Descrizione del componente
*  @param IN p_quantita - Quantità del componente
*  @param IN p_prezzo - Prezzo del componente
*  @param IN p_email_creatore - Email del creatore del progetto che richiede l'inserimento
*/
CREATE PROCEDURE sp_componente_insert(
	IN p_nome_componente VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_descrizione TEXT,
	IN p_quantita INT,
	IN p_prezzo DECIMAL(10, 2),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo hardware
	CALL sp_componente_check(p_nome_componente, p_nome_progetto, p_email_creatore, TRUE);

	-- Se i controlli passano, inserisco il componente
	INSERT INTO COMPONENTE (nome_componente, nome_progetto, descrizione, quantita, prezzo)
	VALUES (p_nome_componente, p_nome_progetto, p_descrizione, p_quantita, p_prezzo);
	COMMIT;
END//

/*
*  PROCEDURE: sp_componente_delete
*  PURPOSE: Rimozione di un componente per un progetto hardware.
*  USED BY: CREATORE
*  NOTE: Il budget del progetto viene aggiornato in base alla quantità e al prezzo del componente rimosso (vedi trigger trg_update_budget_componente_delete).
*
*  @param IN p_nome_componente - Nome del componente da rimuovere
*  @param IN p_nome_progetto - Nome del progetto hardware a cui appartiene il componente
*  @param IN p_email_creatore - Email del creatore del progetto che richiede la rimozione
*/
CREATE PROCEDURE sp_componente_delete(
	IN p_nome_componente VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo hardware
	--  3. Il componente esista
	CALL sp_componente_check(p_nome_componente, p_nome_progetto, p_email_creatore, FALSE);

	-- Se i controlli passano, rimuovo il componente
	DELETE
	FROM COMPONENTE
	WHERE nome_componente = p_nome_componente
	  AND nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_componente_update
*  PURPOSE: Aggiornamento di un componente per un progetto hardware.
*  USED BY: CREATORE
*  NOTE:
*   - Il budget del progetto viene aggiornato in base alla differenza tra il prezzo e la quantità del componente prima e dopo l'aggiornamento
*     (vedi trigger trg_update_budget_componente_update).
*   - Se campi come nome e/o descrizione del componente non sono modificati a livello di interfaccia, non vengono aggiornati e vengono passati
*     nome e/o descrizione attuali.
*
*  @param IN p_nome_componente - Nome del componente da aggiornare
*  @param IN p_nome_progetto - Nome del progetto hardware a cui appartiene il componente
*  @param IN p_descrizione - Nuova descrizione del componente
*  @param IN p_quantita - Nuova quantità del componente
*  @param IN p_prezzo - Nuovo prezzo del componente
*  @param IN p_email_creatore - Email del creatore del progetto che richiede l'aggiornamento
*/
CREATE PROCEDURE sp_componente_update(
	IN p_nome_componente VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_descrizione TEXT,
	IN p_quantita INT,
	IN p_prezzo DECIMAL(10, 2),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo hardware
	--  3. Il componente esista
	CALL sp_componente_check(p_nome_componente, p_nome_progetto, p_email_creatore, FALSE);

	-- Se i controlli passano, aggiorno il componente
	UPDATE COMPONENTE
	SET descrizione = p_descrizione,
	    quantita    = p_quantita,
	    prezzo      = p_prezzo
	WHERE nome_componente = p_nome_componente
	  AND nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_componente_selectAllByProgetto
*  PURPOSE: Restituisce la lista dei componenti di un progetto hardware.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto hardware di cui si vogliono ottenere i componenti
*/
CREATE PROCEDURE sp_componente_selectAllByProgetto(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT nome_componente, descrizione, quantita, prezzo
	FROM COMPONENTE
	WHERE nome_progetto = p_nome_progetto;
	COMMIT;
END//

-- PROFILO:
--  sp_profilo_insert
--  sp_profilo_delete
--  sp_profilo_selectAllByProgetto

/*
*  PROCEDURE: sp_profilo_insert
*  PURPOSE: Inserimento di un profilo per un progetto software.
*  USED BY: CREATORE
*
*  @param IN p_nome_profilo - Nome del profilo da inserire
*  @param IN p_nome_progetto - Nome del progetto software a cui appartiene il profilo
*  @param IN p_email_creatore - Email del creatore del progetto che richiede l'inserimento
*/
CREATE PROCEDURE sp_profilo_insert(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo software
	CALL sp_profilo_check(p_nome_profilo, p_email_creatore, p_nome_progetto, TRUE);

	-- Se i controlli passano, inserisco il profilo
	INSERT INTO PROFILO (nome_profilo, nome_progetto)
	VALUES (p_nome_profilo, p_nome_progetto);
	COMMIT;
END//

/*
*  PROCEDURE: sp_profilo_delete
*  PURPOSE: Rimozione di un profilo per un progetto software.
*  USED BY: CREATORE
*  NOTE: La rimozione del profilo comporta la rimozione (se esiste) del PARTECIPANTE associato a quel profilo.
*
*  @param IN p_nome_profilo - Nome del profilo da rimuovere
*  @param IN p_nome_progetto - Nome del progetto software a cui appartiene il profilo
*  @param IN p_email_creatore - Email del creatore del progetto che richiede la rimozione
*/
CREATE PROCEDURE sp_profilo_delete(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo software
	--  3. Il profilo esista
	CALL sp_profilo_check(p_nome_profilo, p_email_creatore, p_nome_progetto, FALSE);

	-- Se i controlli passano, rimuovo il profilo
	DELETE
	FROM PROFILO
	WHERE nome_profilo = p_nome_profilo
	  AND nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_profilo_selectAllByProgetto
*  PURPOSE: Restituisce la lista dei profili di un progetto software, assieme alle competenze e ai livelli richiesti.
*  NOTE: Le competenze sono restituite in formato 'competenza|livello_richiesto'. Vengono raggruppate per profilo.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto software di cui si vogliono ottenere i profili
*/
CREATE PROCEDURE sp_profilo_selectAllByProgetto(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT P.nome_profilo,
	       GROUP_CONCAT(CONCAT(sp.competenza, '|', sp.livello_richiesto) SEPARATOR ',') AS skills
	FROM PROFILO P
		     JOIN SKILL_PROFILO sp
		          ON P.nome_profilo = sp.nome_profilo
			          AND P.nome_progetto = sp.nome_progetto
	WHERE P.nome_progetto = p_nome_progetto
	GROUP BY P.nome_profilo;
	COMMIT;
END//

-- SKILL_PROFILO:
--  sp_skill_profilo_insert
--  sp_skill_profilo_delete
--  sp_skill_profilo_update

/*
*  PROCEDURE: sp_skill_profilo_insert
*  PURPOSE: Inserimento di una skill per un profilo di un progetto.
*  USED BY: CREATORE
*
*  @param IN p_nome_profilo - Nome del profilo del progetto software per cui inserire la skill
*  @param IN p_nome_progetto - Nome del progetto software a cui appartiene il profilo
*  @param IN p_email_creatore - Email del creatore del progetto che richiede l'inserimento
*  @param IN p_competenza - Competenza/skill richiesta per il profilo del progetto da inserire
*  @param IN p_livello_richiesto - Livello richiesto per la competenza nel profilo del progetto (da 0 a 5)
*/
CREATE PROCEDURE sp_skill_profilo_insert(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_competenza VARCHAR(100),
	IN p_livello_richiesto TINYINT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo software
	--  3. Il profilo esista
	CALL sp_skill_profilo_check(p_nome_profilo, p_email_creatore, p_nome_progetto, p_competenza, TRUE);

	-- Se i controlli passano, inserisco la skill nel profilo del progetto
	INSERT INTO SKILL_PROFILO (nome_profilo, competenza, nome_progetto, livello_richiesto)
	VALUES (p_nome_profilo, p_competenza, p_nome_progetto, p_livello_richiesto);
	COMMIT;
END//

/*
*  PROCEDURE: sp_skill_profilo_delete
*  PURPOSE: Rimozione di una skill per un profilo di un progetto software.
*  USED BY: CREATORE
*  NOTE: La rimozione della skill comporta la rimozione (se esiste) del PARTECIPANTE associato a quel profilo e quella skill.
*
*  @param IN p_nome_profilo - Nome del profilo del progetto software per cui rimuovere la skill
*  @param IN p_nome_progetto - Nome del progetto software a cui appartiene il profilo
*  @param IN p_competenza - Competenza/skill richiesta per il profilo del progetto da rimuovere
*  @param IN p_email_creatore - Email del creatore del progetto che richiede la rimozione
*/
CREATE PROCEDURE sp_skill_profilo_delete(
	IN p_nome_profilo VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_competenza VARCHAR(100),
	IN p_email_creatore VARCHAR(100)
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo software
	--  3. Il profilo esista
	--  4. Il profilo abbia la competenza richiesta
	CALL sp_skill_profilo_check(p_nome_profilo, p_email_creatore, p_nome_progetto, p_competenza, FALSE);

	-- Se i controlli passano, rimuovo la skill dal profilo
	DELETE
	FROM SKILL_PROFILO
	WHERE nome_profilo = p_nome_profilo
	  AND competenza = p_competenza
	  AND nome_progetto = p_nome_progetto;
	COMMIT;
END//

/*
*  PROCEDURE: sp_skill_profilo_update
*  PURPOSE: Aggiornamento del livello richiesto di una skill per un profilo di un progetto software.
*  USED BY: CREATORE
*  NOTE: Se un PARTECIPANTE potenziale non soddisfa il nuovo livello richiesto, la candidatura viene automaticamente rifiutata
*        (vedi trigger trg_rifiuta_candidature_skill_profilo_update)
*
*  @param IN p_nome_profilo - Nome del profilo del progetto software per cui aggiornare il livello richiesto della skill
*  @param IN p_competenza - Competenza/skill richiesta per il profilo del progetto da aggiornare
*  @param IN p_nome_progetto - Nome del progetto software a cui appartiene il profilo
*  @param IN p_email_creatore - Email del creatore del progetto che richiede l'aggiornamento
*  @param IN p_nuovo_livello_richiesto - Nuovo livello richiesto per la competenza nel profilo del progetto (da 0 a 5)
*/
CREATE PROCEDURE sp_skill_profilo_update(
	IN p_nome_profilo VARCHAR(100),
	IN p_competenza VARCHAR(100),
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_nuovo_livello_richiesto TINYINT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. Il progetto sia di tipo software
	--  3. Il profilo esista
	--  4. Il profilo abbia la competenza richiesta
	CALL sp_skill_profilo_check(p_nome_profilo, p_email_creatore, p_nome_progetto, p_competenza, FALSE);

	-- Se i controlli passano, aggiorno il livello richiesto della skill
	UPDATE SKILL_PROFILO
	SET livello_richiesto = p_nuovo_livello_richiesto
	WHERE nome_profilo = p_nome_profilo
	  AND competenza = p_competenza
	  AND nome_progetto = p_nome_progetto;
	COMMIT;
END//

-- FOTO:
--  sp_foto_insert
--  sp_foto_delete
--  sp_foto_selectAll

/*
*  PROCEDURE: sp_foto_insert
*  PURPOSE: Inserimento di una foto per un progetto.
*  USED BY: CREATORE
*
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene la foto
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_foto - Foto da inserire
*/
CREATE PROCEDURE sp_foto_insert(
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_foto MEDIUMBLOB
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che l'utente sia il creatore del progetto
	CALL sp_foto_check(p_nome_progetto, p_email_creatore, NULL, TRUE);

	-- Se il controllo passa, aggiungo la foto
	INSERT INTO FOTO (nome_progetto, foto)
	VALUES (p_nome_progetto, p_foto);
	COMMIT;
END//

/*
*  PROCEDURE: sp_foto_delete
*  PURPOSE: Rimozione di una foto per un progetto.
*  USED BY: CREATORE
*
*  @param IN p_nome_progetto - Nome del progetto a cui appartiene la foto
*  @param IN p_email_creatore - Email del creatore del progetto
*  @param IN p_foto_id - ID della foto da rimuovere
*/
CREATE PROCEDURE sp_foto_delete(
	IN p_nome_progetto VARCHAR(100),
	IN p_email_creatore VARCHAR(100),
	IN p_foto_id INT
)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			ROLLBACK;
			RESIGNAL;
		END;
	START TRANSACTION;
	-- Controllo che:
	--  1. L'utente sia il creatore del progetto
	--  2. La foto esista
	CALL sp_foto_check(p_nome_progetto, p_email_creatore, p_foto_id, FALSE);

	-- Se i controlli passano, rimuovo la foto
	DELETE
	FROM FOTO
	WHERE nome_progetto = p_nome_progetto
	  AND id = p_foto_id;
	COMMIT;
END//

/*
*  PROCEDURE: sp_foto_selectAll
*  PURPOSE: Visualizzazione di tutte le foto di un progetto.
*  USED BY: ALL
*
*  @param IN p_nome_progetto - Nome del progetto di cui visualizzare le foto
*/
CREATE PROCEDURE sp_foto_selectAll(
	IN p_nome_progetto VARCHAR(100)
)
BEGIN
	START TRANSACTION;
	SELECT id, foto
	FROM FOTO
	WHERE nome_progetto = p_nome_progetto;
	COMMIT;
END//

DELIMITER ;

-- ==================================================
-- VISTE
-- ==================================================

-- Le 3 seguenti viste sono accessibili a tutti gli utenti autenticati a livello di interfaccia, e non sono accessibili a utenti non autenticati.
-- Sono visibili nella pagina statistiche.php ("Statistiche" nel navbar).

-- Classifica dei top 3 utenti creatori, in base al loro valore di affidabilità
CREATE OR REPLACE VIEW view_classifica_creatori_affidabilita AS
SELECT U.nickname
FROM CREATORE C
	     JOIN UTENTE U ON C.email_utente = U.email
ORDER BY affidabilita DESC
LIMIT 3;

-- Stored procedure per visualizzare la classifica dei top 3 creatori più affidabili
DELIMITER //
CREATE PROCEDURE view_classifica_creatori_affidabilita()
BEGIN
	SELECT * FROM view_classifica_creatori_affidabilita;
END//
DELIMITER ;

-- Classifica dei top 3 progetti APERTI che sono più vicini al proprio completamento
CREATE OR REPLACE VIEW view_classifica_progetti_completamento AS
SELECT P.nome,
       P.budget,
       IFNULL((SELECT SUM(importo)
               FROM FINANZIAMENTO
               WHERE nome_progetto = P.nome), 0) AS tot_finanziamenti
FROM PROGETTO P
WHERE P.stato = 'aperto'
ORDER BY (tot_finanziamenti / budget) DESC
LIMIT 3;

-- Stored procedure per visualizzare la classifica dei top 3 progetti più vicini al completamento
DELIMITER //
CREATE PROCEDURE view_classifica_progetti_completamento()
BEGIN
	SELECT * FROM view_classifica_progetti_completamento;
END//
DELIMITER ;

-- Classifica dei top 3 utenti, in base al TOTALE di finanziamenti erogati
CREATE OR REPLACE VIEW view_classifica_utenti_finanziamento AS
SELECT U.nickname
FROM UTENTE U
	     JOIN FINANZIAMENTO F ON U.email = F.email_utente
GROUP BY U.email
ORDER BY SUM(F.importo) DESC
LIMIT 3;

-- Stored procedure per visualizzare la classifica dei top 3 utenti con più finanziamenti erogati
DELIMITER //
CREATE PROCEDURE view_classifica_utenti_finanziamento()
BEGIN
	SELECT * FROM view_classifica_utenti_finanziamento;
END//
DELIMITER ;

-- ==================================================
-- TRIGGERS
-- ==================================================

-- Similmente al blocco delle stored procedures, i trigger sono divisi in base alla tabella di riferimento, con la seguente sintassi generale...
-- NOME_TABELLA:
--  trg_nome_trigger

DELIMITER //

-- CREATORE:
--  trg_update_affidabilita_progetto
--  trg_update_affidabilita_finanziamento
--  trg_incrementa_progetti_creati

-- Trigger per aggiornare l'affidabilità di un creatore quando crea un progetto
CREATE TRIGGER trg_update_affidabilita_progetto
	AFTER INSERT
	ON PROGETTO
	FOR EACH ROW
BEGIN
	DECLARE tot_progetti INT;
	DECLARE progetti_finanziati INT;
	DECLARE new_aff DECIMAL(5, 2);

	-- Numero totale di progetti creati dal creatore
	SELECT COUNT(*)
	INTO tot_progetti
	FROM PROGETTO
	WHERE email_creatore = NEW.email_creatore;

	-- Numero di progetti del creatore che hanno ricevuto almeno un finanziamento
	SELECT COUNT(DISTINCT P.nome)
	INTO progetti_finanziati
	FROM PROGETTO P
		     JOIN FINANZIAMENTO F ON P.nome = F.nome_progetto
	WHERE P.email_creatore = NEW.email_creatore;

	-- Calcola la nuova affidabilità del creatore
	IF tot_progetti > 0 THEN
		SET new_aff = (progetti_finanziati / tot_progetti) * 100;
	ELSE
		SET new_aff = 0;
	END IF;

	UPDATE CREATORE
	SET affidabilita = new_aff
	WHERE email_utente = NEW.email_creatore;
END//

-- Trigger per aggiornare l'affidabilità di un creatore quando un progetto da lui creato viene finanziato
CREATE TRIGGER trg_update_affidabilita_finanziamento
	AFTER INSERT
	ON FINANZIAMENTO
	FOR EACH ROW
BEGIN
	DECLARE email VARCHAR(100);
	DECLARE tot_progetti INT;
	DECLARE progetti_finanziati INT;
	DECLARE new_aff DECIMAL(5, 2);

	-- Recupera l'email del creatore del progetto
	SELECT email_creatore
	INTO email
	FROM PROGETTO
	WHERE nome = NEW.nome_progetto;

	-- Numero totale di progetti creati dal creatore
	SELECT COUNT(*)
	INTO tot_progetti
	FROM PROGETTO
	WHERE email_creatore = email;

	-- Numero di progetti del creatore che hanno ricevuto almeno un finanziamento
	SELECT COUNT(DISTINCT P.nome)
	INTO progetti_finanziati
	FROM PROGETTO P
		     JOIN FINANZIAMENTO F ON P.nome = F.nome_progetto
	WHERE P.email_creatore = email;

	-- Calcola la nuova affidabilità del creatore
	IF tot_progetti > 0 THEN
		SET new_aff = (progetti_finanziati / tot_progetti) * 100;
	ELSE
		SET new_aff = 0;
	END IF;

	UPDATE CREATORE
	SET affidabilita = new_aff
	WHERE email_utente = email;
END//

-- Trigger per aumentare il numero di progetti creati da un creatore
CREATE TRIGGER trg_incrementa_progetti_creati
	AFTER INSERT
	ON PROGETTO
	FOR EACH ROW
BEGIN
	UPDATE CREATORE
	SET nr_progetti = nr_progetti + 1
	WHERE email_utente = NEW.email_creatore;
END//

-- PROGETTO:
--  trg_update_stato_progetto
--  trg_update_budget_componente_insert
--  trg_update_budget_componente_delete
--  trg_update_budget_componente_update

-- Trigger per cambiare lo stato di un progetto in 'chiuso' quando il budget è stato raggiunto
CREATE TRIGGER trg_update_stato_progetto
	AFTER INSERT
	ON FINANZIAMENTO
	FOR EACH ROW
BEGIN
	DECLARE tot_finanziamento DECIMAL(10, 2);
	DECLARE budget_progetto DECIMAL(10, 2);

	-- Calcola il budget del progetto
	SELECT budget
	INTO budget_progetto
	FROM PROGETTO
	WHERE nome = NEW.nome_progetto;

	-- Calcola il totale del finanziamento per il progetto
	SELECT SUM(importo)
	INTO tot_finanziamento
	FROM FINANZIAMENTO
	WHERE nome_progetto = NEW.nome_progetto;

	-- Se il totale del finanziamento è >= al budget del progetto, allora lo stato del progetto diventa 'chiuso'
	IF tot_finanziamento >= budget_progetto THEN
		UPDATE PROGETTO
		SET stato = 'chiuso'
		WHERE nome = NEW.nome_progetto;
	END IF;
END//

-- Trigger per aggiornare il budget di un progetto hardware quando un componente viene aggiunto
CREATE TRIGGER trg_update_budget_componente_insert
	BEFORE INSERT
	ON COMPONENTE
	FOR EACH ROW
BEGIN
	DECLARE tot_componenti DECIMAL(10, 2);
	DECLARE budget_progetto DECIMAL(10, 2);
	DECLARE eccesso DECIMAL(10, 2);

	-- Controllo che il progetto sia ancora aperto
	IF (SELECT stato
	    FROM PROGETTO
	    WHERE nome = NEW.nome_progetto) = 'chiuso' THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO CHIUSO';
	END IF;

	-- Recupera il budget del progetto
	SELECT budget
	INTO budget_progetto
	FROM PROGETTO
	WHERE nome = NEW.nome_progetto;

	-- Calcola il costo totale dei componenti del progetto
	SELECT IFNULL(SUM(prezzo * quantita), 0) -- Per sicurezza anche se deve esistere sempre almeno un componente a prescindere
	INTO tot_componenti
	FROM COMPONENTE
	WHERE nome_progetto = NEW.nome_progetto;

	-- Determina l'eccesso di budget
	SET eccesso = (tot_componenti + (NEW.prezzo * NEW.quantita)) - budget_progetto;

	-- Se l'eccesso è > 0, allora aggiorna il budget del progetto
	IF eccesso > 0 THEN
		UPDATE PROGETTO
		SET budget = budget + eccesso
		WHERE nome = NEW.nome_progetto;
	END IF;
END //

-- Trigger per aggiornare il budget di un progetto hardware quando un componente viene rimosso
CREATE TRIGGER trg_update_budget_componente_delete
	BEFORE DELETE
	ON COMPONENTE
	FOR EACH ROW
BEGIN
	DECLARE budget_progetto DECIMAL(10, 2);
	DECLARE new_budget DECIMAL(10, 2);

	-- Controllo che il progetto sia ancora aperto
	IF (SELECT stato
	    FROM PROGETTO
	    WHERE nome = OLD.nome_progetto) = 'chiuso' THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO CHIUSO';
	END IF;

	-- Controllo che ci sia almeno un componente rimanente
	IF (SELECT COUNT(*)
	    FROM COMPONENTE
	    WHERE nome_progetto = OLD.nome_progetto) = 1 THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'IMPOSSIBILE RIMUOVERE ULTIMO COMPONENTE';
	END IF;

	-- Recupera il budget del progetto
	SELECT budget
	INTO budget_progetto
	FROM PROGETTO
	WHERE nome = OLD.nome_progetto;

	-- Determina il budget del progetto senza il componente rimosso
	SET new_budget = budget_progetto - (OLD.prezzo * OLD.quantita);

	-- Aggiorna il budget del progetto
	UPDATE PROGETTO
	SET budget = new_budget
	WHERE nome = OLD.nome_progetto;
END //

-- Trigger per aggiornare il budget di un progetto hardware quando un componente viene aggiornato
CREATE TRIGGER trg_update_budget_componente_update
	BEFORE UPDATE
	ON COMPONENTE
	FOR EACH ROW
BEGIN
	DECLARE tot_componenti DECIMAL(10, 2);
	DECLARE budget_progetto DECIMAL(10, 2);
	DECLARE eccesso DECIMAL(10, 2);

	-- Controllo che il progetto sia ancora aperto
	IF (SELECT stato
	    FROM PROGETTO
	    WHERE nome = NEW.nome_progetto) = 'chiuso' THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO CHIUSO';
	END IF;

	-- Controllo che la quantità del componente sia > 0
	IF NEW.quantita <= 0 THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'QUANTITY COMPONENTE DEVE ESSERE > 0';
	END IF;

	-- Recupera il budget del progetto
	SELECT budget
	INTO budget_progetto
	FROM PROGETTO
	WHERE nome = NEW.nome_progetto;

	-- Calcola il costo totale dei componenti del progetto, escludendo il componente corrente
	SELECT IFNULL(SUM(prezzo * quantita), 0)
	INTO tot_componenti
	FROM COMPONENTE
	WHERE nome_progetto = NEW.nome_progetto
	  AND NOT (nome_componente = OLD.nome_componente AND nome_progetto = OLD.nome_progetto);
	-- Nel caso in cui il nome del componente sia cambiato

	-- Determina l'eccesso di budget con il nuovo componente
	SET eccesso = (tot_componenti + (NEW.prezzo * NEW.quantita)) - budget_progetto;

	-- Vale anche se l'"eccesso" è negativo, per ridurre il budget
	UPDATE PROGETTO
	SET budget = budget + eccesso
	WHERE nome = NEW.nome_progetto;
END //

-- PARTECIPANTE:
--  trg_rifiuta_candidature_skill_profilo_update
--  trg_rifiuta_candidatura_livello_effettivo_insufficiente
--  trg_delete_candidature_progetto_chiuso
--  trg_delete_candidature_profilo_rimosso_da_progetto

-- Trigger per rifiutare automaticamente eventuali candidature se il livello_richiesto della competenza del relativo profilo viene aumentata
CREATE TRIGGER trg_rifiuta_candidature_skill_profilo_update
	AFTER UPDATE
	ON SKILL_PROFILO
	FOR EACH ROW
BEGIN
	UPDATE PARTECIPANTE P
	SET stato = 'rifiutato'
	WHERE P.nome_profilo = NEW.nome_profilo
	  AND P.nome_progetto = NEW.nome_progetto
	  AND P.stato = 'potenziale'
	  AND NOT EXISTS (SELECT 1
	                  FROM SKILL_CURRICULUM SC
	                  WHERE SC.email_utente = P.email_utente
		                AND SC.competenza = NEW.competenza
		                AND SC.livello_effettivo >= NEW.livello_richiesto);
END//


-- Trigger per rifiutare automaticamente candidature se il livello_effettivo non è sufficiente rispetto al livello_richiesto di ogni competenza del profilo
DELIMITER //
CREATE TRIGGER trg_rifiuta_candidatura_livello_effettivo_insufficiente
	BEFORE INSERT
	ON PARTECIPANTE
	FOR EACH ROW
BEGIN
	-- Controllo che il progetto sia ancora aperto
	IF (SELECT stato FROM PROGETTO WHERE nome = NEW.nome_progetto) = 'chiuso' THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'PROGETTO CHIUSO';
	END IF;

	-- Controllo che per ogni competenza richiesta dal profilo, il livello effettivo del candidato sia >= al livello richiesto
	IF EXISTS (SELECT 1
	           FROM SKILL_PROFILO sp
	           WHERE sp.nome_profilo = NEW.nome_profilo
		         AND sp.nome_progetto = NEW.nome_progetto
		         AND NOT EXISTS (SELECT 1
		                         FROM SKILL_CURRICULUM sc
		                         WHERE sc.email_utente = NEW.email_utente
			                       AND sc.competenza = sp.competenza
			                       AND sc.livello_effettivo >= sp.livello_richiesto)) THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'LIVELLO EFFETTIVO NON SUFFICIENTE PER IL PROFILO';
	END IF;
END//

-- Trigger per rifiutare automaticamente le candidature se il progetto viene chiuso
CREATE TRIGGER trg_delete_candidature_progetto_chiuso
	AFTER UPDATE
	ON PROGETTO
	FOR EACH ROW
BEGIN
	IF NEW.stato = 'chiuso' THEN
		UPDATE PARTECIPANTE
		SET stato = 'rifiutato'
		WHERE nome_progetto = NEW.nome
		  AND stato = 'potenziale';
	END IF;
END//

-- FINANZIAMENTO:
--  trg_rifiuta_finanziamento_utente_giorno

-- Trigger per rifiutare automaticamente un finanziamento se l'utente ha già finanziato il progetto nello stesso giorno
CREATE TRIGGER trg_rifiuta_finanziamento_utente_giorno
	BEFORE INSERT
	ON FINANZIAMENTO
	FOR EACH ROW
BEGIN
	DECLARE data_finanziamento DATE;
	DECLARE num_finanziamenti INT;

	-- Recupera la data attuale
	SET data_finanziamento = CURDATE();

	-- Conta il numero di finanziamenti effettuati dall'utente per il progetto nello stesso giorno
	SELECT COUNT(*)
	INTO num_finanziamenti
	FROM FINANZIAMENTO
	WHERE email_utente = NEW.email_utente
	  AND nome_progetto = NEW.nome_progetto
	  AND data = data_finanziamento;

	-- Se il numero di finanziamenti è > 0, rifiuta il finanziamento
	IF num_finanziamenti > 0 THEN
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'FINANZIAMENTO GIA\' EFFETTUATO NELLO STESSO GIORNO';
	END IF;
END//

DELIMITER ;

-- ==================================================
-- EVENTI
-- ==================================================

DELIMITER //

-- Evento per chiudere automaticamente i progetti scaduti, eseguito ogni giorno
CREATE EVENT ev_chiudi_progetti_scaduti
	ON SCHEDULE EVERY 1 DAY
	DO
	BEGIN
		UPDATE PROGETTO
		SET stato = 'chiuso'
		WHERE stato = 'aperto'
		  AND data_limite < CURDATE();
	END//

DELIMITER ;