# noinspection SpellCheckingInspectionForFile

-- ==================================================
-- DATABASE
-- ==================================================

DROP DATABASE IF EXISTS BOSTARTER;
CREATE DATABASE BOSTARTER;
USE BOSTARTER;

-- ==================================================
-- TABELLE
-- ==================================================

-- 1. UTENTE
CREATE TABLE UTENTE
(
    email         VARCHAR(100) NOT NULL,
    password      VARCHAR(255) NOT NULL,
    nickname      VARCHAR(50)  NOT NULL UNIQUE CHECK ( LENGTH(nickname) >= 3 ),                -- Minimo 3 caratteri
    nome          VARCHAR(50)  NOT NULL,
    cognome       VARCHAR(50)  NOT NULL,
    anno_nascita  INT          NOT NULL CHECK ( anno_nascita > 1925 AND anno_nascita < 2007 ), -- 18 < età < 100, per ovviare problemi legali
    luogo_nascita VARCHAR(50)  NOT NULL,
    PRIMARY KEY (email)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
-- Uso di utf8mb4 (full UTF-8 support) per supportare eventuali caratteri speciali e emoji

-- 2. ADMIN
CREATE TABLE ADMIN
(
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
CREATE TABLE CREATORE
(
    email_utente VARCHAR(100) NOT NULL,
    nr_progetti  INT UNSIGNED  DEFAULT 0,                                                   -- Numero di progetti creati 0..255
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
CREATE TABLE PROGETTO
(
    nome             VARCHAR(100)   NOT NULL,
    email_creatore   VARCHAR(100)   NOT NULL,
    descrizione      TEXT           NOT NULL CHECK ( LENGTH(descrizione) >= 10 ), -- Minimo 10 caratteri
    budget           DECIMAL(10, 2) NOT NULL CHECK ( budget > 0 ),
    stato            ENUM ('aperto','chiuso') DEFAULT 'aperto',
    data_inserimento DATE           NOT NULL  DEFAULT CURRENT_DATE,
    data_limite      DATE           NOT NULL CHECK ( data_limite > CURRENT_DATE ),
    PRIMARY KEY (nome),
    CONSTRAINT fk_progetto_creatore
        FOREIGN KEY (email_creatore)
            REFERENCES UTENTE (email)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 5. PROGETTO_SOFTWARE
CREATE TABLE PROGETTO_SOFTWARE
(
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
CREATE TABLE PROGETTO_HARDWARE
(
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
CREATE TABLE FOTO
(
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
CREATE TABLE REWARD
(
    codice        VARCHAR(50)    NOT NULL,
    nome_progetto VARCHAR(100)   NOT NULL,
    descrizione   TEXT           NOT NULL CHECK ( LENGTH(descrizione) >= 10 ), -- Minimo 10 caratteri
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
CREATE TABLE COMPONENTE
(
    nome_componente VARCHAR(100)   NOT NULL CHECK ( LENGTH(nome_componente) >= 3 ), -- Minimo 3 caratteri
    nome_progetto   VARCHAR(100)   NOT NULL,
    descrizione     TEXT           NOT NULL CHECK ( LENGTH(descrizione) >= 10 ),    -- Minimo 10 caratteri
    quantita        INT            NOT NULL CHECK ( quantita > 0 ),                 -- Business Rule #3
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
CREATE TABLE PROFILO
(
    nome_profilo VARCHAR(100) NOT NULL CHECK ( LENGTH(nome_profilo) >= 10 ), -- Minimo 10 caratteri
    PRIMARY KEY (nome_profilo)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 11. SKILL
CREATE TABLE SKILL
(
    competenza VARCHAR(100) NOT NULL CHECK ( LENGTH(competenza) >= 10 ), -- Minimo 10 caratteri
    PRIMARY KEY (competenza)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 12. FINANZIAMENTO
CREATE TABLE FINANZIAMENTO
(
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
CREATE TABLE COMMENTO
(
    id            INT          NOT NULL AUTO_INCREMENT,
    email_utente  VARCHAR(100) NOT NULL,
    nome_progetto VARCHAR(100) NOT NULL,
    data          DATE         NOT NULL DEFAULT CURRENT_DATE,
    testo         TEXT         NOT NULL CHECK ( LENGTH(testo) >= 10 ), -- Minimo 10 caratteri
    risposta      TEXT         NULL,                                   -- Business Rule #8
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
CREATE TABLE SKILL_CURRICULUM
(
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
CREATE TABLE SKILL_PROFILO
(
    nome_profilo      VARCHAR(100) NOT NULL,
    competenza        VARCHAR(100) NOT NULL,
    livello_richiesto TINYINT      NOT NULL CHECK ( livello_richiesto BETWEEN 0 AND 5 ), -- Business Rule #2
    PRIMARY KEY (nome_profilo, competenza),
    CONSTRAINT fk_skprof_profilo
        FOREIGN KEY (nome_profilo)
            REFERENCES PROFILO (nome_profilo)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT fk_skprof_skill
        FOREIGN KEY (competenza)
            REFERENCES SKILL (competenza)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 16. PARTECIPANTE
CREATE TABLE PARTECIPANTE
(
    email_utente  VARCHAR(100) NOT NULL,
    nome_progetto VARCHAR(100) NOT NULL,
    stato         ENUM ('accettato','rifiutato','potenziale') DEFAULT 'potenziale',
    PRIMARY KEY (email_utente, nome_progetto),
    CONSTRAINT fk_part_utente
        FOREIGN KEY (email_utente)
            REFERENCES UTENTE (email)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT fk_part_progetto
        FOREIGN KEY (nome_progetto)
            REFERENCES PROGETTO (nome)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- 17. PROFILO_PROGETTO
CREATE TABLE PROFILO_PROGETTO
(
    nome_progetto VARCHAR(100) NOT NULL,
    nome_profilo  VARCHAR(100) NOT NULL,
    PRIMARY KEY (nome_progetto, nome_profilo),
    CONSTRAINT fk_pproj_progetto
        FOREIGN KEY (nome_progetto)
            REFERENCES PROGETTO (nome)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT fk_pproj_profilo
        FOREIGN KEY (nome_profilo)
            REFERENCES PROFILO (nome_profilo)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ==================================================
-- STORED PROCEDURES
-- ==================================================

DELIMITER //

-- (ALL) Registrazione di un utente con o senza ruolo di creatore
CREATE PROCEDURE sp_registra_utente(
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    IN p_nickname VARCHAR(50),
    IN p_nome VARCHAR(50),
    IN p_cognome VARCHAR(50),
    IN p_anno_nascita INT,
    IN p_luogo_nascita VARCHAR(50),
    IN p_creator BOOLEAN
)
BEGIN
    START TRANSACTION; -- Uso di transazione per garantire l'integrità dei dati
    INSERT INTO UTENTE (email, password, nickname, nome, cognome, anno_nascita, luogo_nascita)
    VALUES (p_email, p_password, p_nickname, p_nome, p_cognome, p_anno_nascita, p_luogo_nascita);

    IF p_creator THEN
        INSERT INTO CREATORE (email_utente)
        VALUES (p_email);
    END IF;
    COMMIT;
END//

-- (ALL) Inserimento di skill in un curriculum utente
CREATE PROCEDURE sp_inserisci_skill_curriculum(
    IN p_email VARCHAR(100),
    IN p_competenza VARCHAR(100),
    IN p_livello TINYINT
)
BEGIN
    START TRANSACTION;
    INSERT INTO SKILL_CURRICULUM (email_utente, competenza, livello_effettivo)
    VALUES (p_email, p_competenza, p_livello);
    COMMIT;
END//

-- (ALL) Visualizzazione dei progetti disponibili
CREATE PROCEDURE sp_visualizza_progetti()
BEGIN
    SELECT * FROM PROGETTO;
END//

-- (ALL) Finanziamento di un progetto aperto da parte di un utente (anche creatore)
CREATE PROCEDURE sp_finanzia_progetto(
    IN p_data DATE,
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_codice_reward VARCHAR(50), -- Si presuppone che il codice sia scelto dall'utente al livello di interfaccia
    IN p_importo DECIMAL(10, 2)
)
BEGIN
    DECLARE stato_progetto ENUM ('aperto', 'chiuso');

    START TRANSACTION;
    -- Controllo che il progetto sia aperto
    SELECT stato
    INTO stato_progetto
    FROM PROGETTO
    WHERE nome = p_nome_progetto;

    IF stato_progetto = 'aperto' THEN
        INSERT INTO FINANZIAMENTO (data, email_utente, nome_progetto, codice_reward, importo)
        VALUES (p_data, p_email, p_nome_progetto, p_codice_reward, p_importo);
    ELSE
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Il progetto non è aperto per finanziamenti';
    END IF;
    COMMIT;
END//

-- (ALL) Inserimento di un commento a un progetto
CREATE PROCEDURE sp_inserisci_commento(
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_data DATE,
    IN p_testo TEXT
)
BEGIN
    START TRANSACTION;
    INSERT INTO COMMENTO (email_utente, nome_progetto, data, testo)
    VALUES (p_email, p_nome_progetto, p_data, p_testo);
    COMMIT;
END//

-- (ALL) Inserimento di una candidatura a un progetto software
-- TODO: EVALUATE IF USER QUALIFICATION CHECK IS DONE HERE OR AT PHP-LEVEL
CREATE PROCEDURE sp_inserisci_candidatura(
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
    START TRANSACTION;
    INSERT INTO PARTECIPANTE (email_utente, nome_progetto, stato)
    VALUES (p_email, p_nome_progetto, 'potenziale');
    COMMIT;
END//

DELIMITER ;

-- ==================================================
-- VISTE
-- ==================================================

-- Classifica dei top 3 utenti creatori, in base al loro valore di affidabilità
CREATE VIEW view_classifica_creatori_affidabilita AS
SELECT U.nickname
FROM CREATORE C
         JOIN UTENTE U ON C.email_utente = U.email
ORDER BY affidabilita DESC
LIMIT 3;

-- Classifica dei top 3 progetti APERTI che sono più vicini al proprio completamento
CREATE VIEW view_classifica_progetti_completamento AS
SELECT P.nome,
       (P.budget -
        IFNULL((SELECT SUM(importo) -- Differenza tra il budget e il totale dei finanziamenti per quel progetto
                FROM FINANZIAMENTO
                WHERE nome_progetto = P.nome),
               0)) AS completamento -- Uso di IFNULL per evitare NULL in caso di progetto senza finanziamenti
FROM PROGETTO P
WHERE P.stato = 'aperto'
ORDER BY completamento
LIMIT 3;

-- Classifica dei top 3 utenti, in base al TOTALE di finanziamenti erogati
CREATE VIEW view_classifica_utenti_finanziamento AS
SELECT U.nickname
FROM UTENTE U
         JOIN FINANZIAMENTO F ON U.email = F.email_utente
GROUP BY U.email
ORDER BY SUM(F.importo) DESC
LIMIT 3;

-- ==================================================
-- TRIGGERS
-- ==================================================

-- Trigger per aggiornare l'affidabilità di un creatore quando crea un progetto
DELIMITER //
CREATE TRIGGER trg_update_affidabilita_prog
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
DELIMITER ;

-- Trigger per aggiornare l'affidabilità di un creatore quando un progetto da lui creato viene finanziato
DELIMITER //
CREATE TRIGGER trg_update_affidabilita_fin
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
DELIMITER ;

-- Trigger per cambiare lo stato di un progetto in 'chiuso' quando il budget è stato raggiunto
DELIMITER //
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
DELIMITER ;

-- Trigger per aumentare il numero di progetti creati da un creatore
DELIMITER //
CREATE TRIGGER trg_incrementa_progetti_creati
    AFTER INSERT
    ON PROGETTO
    FOR EACH ROW
BEGIN
    UPDATE CREATORE
    SET nr_progetti = nr_progetti + 1
    WHERE email_utente = NEW.email_creatore;
END//

-- Trigger per aggiornare il budget di un progetto hardware quando un componente viene aggiunto
DELIMITER //
CREATE TRIGGER trg_aggiorna_budget_componente_insert
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
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO CHIUSO';
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
DELIMITER ;

-- Trigger per aggiornare il budget di un progetto hardware quando un componente viene rimosso
DELIMITER //
CREATE TRIGGER trg_aggiorna_budget_componente_delete
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
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO CHIUSO';
    END IF;

    -- Controllo che ci sia almeno un componente rimanente
    IF (SELECT COUNT(*)
        FROM COMPONENTE
        WHERE nome_progetto = OLD.nome_progetto) = 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: IMPOSSIBILE RIMUOVERE ULTIMO COMPONENTE';
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
DELIMITER //
CREATE TRIGGER trg_aggiorna_budget_componente_update
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
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO CHIUSO';
    END IF;

    -- Controllo che la quantità del componente sia > 0
    IF NEW.quantita <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: QUANTITY COMPONENTE DEVE ESSERE > 0';
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
DELIMITER ;

-- ==================================================
-- EVENTI
-- ==================================================

-- Evento per chiudere automaticamente i progetti scaduti, eseguito ogni giorno
DELIMITER //
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