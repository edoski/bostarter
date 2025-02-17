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
    nickname      VARCHAR(50)  NOT NULL UNIQUE CHECK ( LENGTH(nickname) >= 3 ), -- Minimo 3 caratteri
    nome          VARCHAR(50)  NOT NULL,
    cognome       VARCHAR(50)  NOT NULL,
    anno_nascita  INT          NOT NULL CHECK ( anno_nascita < 2007 ),          -- età > 18
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
    nome             VARCHAR(100)   NOT NULL CHECK ( LENGTH(nome) >= 5 ),         -- Minimo 5 caratteri
    email_creatore   VARCHAR(100)   NOT NULL,
    descrizione      TEXT           NOT NULL CHECK ( LENGTH(descrizione) >= 10 ), -- Minimo 10 caratteri
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
    nome_componente VARCHAR(100)   NOT NULL CHECK ( LENGTH(nome_componente) >= 5 ), -- Minimo 5 caratteri
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
    nome_profilo  VARCHAR(100) NOT NULL CHECK ( LENGTH(nome_profilo) >= 5 ), -- Minimo 5 caratteri
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
CREATE TABLE SKILL
(
    competenza VARCHAR(100) NOT NULL CHECK ( LENGTH(competenza) >= 5 ), -- Minimo 5 caratteri
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
    nome_progetto     VARCHAR(100) NOT NULL,
    livello_richiesto TINYINT      NOT NULL CHECK ( livello_richiesto BETWEEN 0 AND 5 ), -- Business Rule #2
    PRIMARY KEY (nome_profilo, competenza, nome_progetto),
    CONSTRAINT fk_skprof_profilo
        FOREIGN KEY (nome_profilo)
            REFERENCES PROFILO (nome_profilo)
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
CREATE TABLE PARTECIPANTE
(
    email_utente  VARCHAR(100) NOT NULL,
    nome_progetto VARCHAR(100) NOT NULL,
    nome_profilo  VARCHAR(100) NOT NULL,
    competenza    VARCHAR(100) NOT NULL,
    stato         ENUM ('accettato','rifiutato','potenziale') DEFAULT 'potenziale',
    PRIMARY KEY (email_utente, nome_progetto, nome_profilo, competenza),
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
        FOREIGN KEY (nome_profilo)
            REFERENCES PROFILO (nome_profilo)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT fk_part_skill
        FOREIGN KEY (competenza)
            REFERENCES SKILL (competenza)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- ==================================================
-- STORED PROCEDURES (HELPER)
-- ==================================================

-- In questo blocco vengono definite le stored procedure di controllo ("helper") che verranno utilizzate dalle stored procedure principali ("main").
-- Sono incluse qui solo i controlli che sono comuni a più stored procedure, per evitare duplicazione di codice. Laddove un controllo sia specifico
-- per una sola stored procedure, esso è incluso direttamente all'interno di quella stored procedure principale, e non qui.

-- SINTASSI GENERALE: sp_nome_procedura_check

-- L'unica eccezione sono le GENERICHE/UTILS sp_util_* che contengono controlli generici che possono essere utilizzati da più stored procedure principali.

DELIMITER //

-- GENERICHE/UTILS: Insieme di controlli generici per tutte le stored procedure principali laddove necessario

-- Controllo che l'utente sia un admin
CREATE PROCEDURE sp_util_is_utente_admin(
    IN p_email VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM ADMIN
                   WHERE email_utente = p_email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: UTENTE NON ADMIN';
    END IF;
END//

-- Controllo che l'utente sia un creatore
CREATE PROCEDURE sp_util_is_utente_creatore(
    IN p_email VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM CREATORE
                   WHERE email_utente = p_email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: UTENTE NON CREATORE';
    END IF;
END//

-- Controllo che il progetto esista e sia stato creato dall'utente
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
            SET MESSAGE_TEXT = 'ERRORE: UTENTE NON CREATORE DEL PROGETTO';
    END IF;
END//

-- Controllo che il progetto esista
CREATE PROCEDURE sp_util_progetto_exists(
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM PROGETTO
                   WHERE nome = p_nome_progetto) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO NON ESISTENTE';
    END IF;
END//

-- Controllo che il progetto sia di tipo software
CREATE PROCEDURE sp_util_is_progetto_software(
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM PROGETTO_SOFTWARE
                   WHERE nome_progetto = p_nome_progetto) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO NON DI TIPO SOFTWARE';
    END IF;
END//

-- Controllo che il progetto sia di tipo hardware
CREATE PROCEDURE sp_util_is_progetto_hardware(
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM PROGETTO_HARDWARE
                   WHERE nome_progetto = p_nome_progetto) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO NON DI TIPO HARDWARE';
    END IF;
END//

-- Controllo che il profilo di un progetto esista
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
            SET MESSAGE_TEXT = 'ERRORE: PROFILO NON ESISTENTE';
    END IF;
END//

-- Controllo che la competenza del profilo di un progetto esista
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
            SET MESSAGE_TEXT = 'ERRORE: COMPETENZA NON PRESENTE NEL PROFILO';
    END IF;
END//

-- Controllo che il commento esista
CREATE PROCEDURE sp_util_commento_exists(
    IN p_id INT
)
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM COMMENTO
                   WHERE id = p_id) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: COMMENTO NON ESISTENTE';
    END IF;
END//

-- SKILL_PROFILO: sp_skill_profilo_check
--  sp_skill_profilo_insert
--  sp_skill_profilo_delete
--  sp_skill_profilo_update

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

-- PROFILO: sp_profilo_check
--  sp_profilo_insert
--  sp_profilo_delete

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

-- COMPONENTE: sp_componente_check
--  sp_componente_insert
--  sp_componente_delete
--  sp_componente_update

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
                SET MESSAGE_TEXT = 'ERRORE: COMPONENTE NON ESISTENTE';
        END IF;
    END IF;
END//

-- PARTECIPANTE: sp_partecipante_check
--  sp_partecipante_creatore_update (sp_partecipante_creatore_check)
--  sp_partecipante_utente_insert (sp_partecipante_utente_check)

-- Controllo generico per entrambe le stored procedure, necessario sia per utente che creatore in ambito partecipante
CREATE PROCEDURE sp_partecipante_check(
    IN p_nome_progetto VARCHAR(100),
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100)
)
BEGIN
    -- Controllo che il progetto sia di tipo software
    CALL sp_util_is_progetto_software(p_nome_progetto);
    -- Controllo che la competenza per quel profilo del progetto esista
    CALL sp_util_skill_profilo_exists(p_nome_profilo, p_nome_progetto, p_competenza);
END//

-- Controlli specifici necessari per quando un creatore vuole accettare o rifiutare un potenziale partecipante
CREATE PROCEDURE sp_partecipante_creatore_check(
    IN p_email_creatore VARCHAR(100),
    IN p_email_candidato VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100)
)
BEGIN
    -- Controllo comune a entrambe le stored procedure
    CALL sp_partecipante_check(p_nome_progetto, p_nome_profilo, p_competenza);
    -- Controllo che l'utente sia il creatore del progetto
    CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);

    -- Controllo che la candidatura esista
    IF NOT EXISTS (SELECT 1
                   FROM PARTECIPANTE
                   WHERE email_utente = p_email_candidato
                     AND nome_progetto = p_nome_progetto
                     AND nome_profilo = p_nome_profilo
                     AND competenza = p_competenza
                     AND stato = 'potenziale') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: CANDIDATURA NON ESISTENTE';
    END IF;
END//

-- Controlli specifici necessari per quando un utente vuole candidarsi a un progetto software
CREATE PROCEDURE sp_partecipante_utente_check(
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100)
)
BEGIN
    -- Controllo comune a entrambe le stored procedure
    CALL sp_partecipante_check(p_nome_progetto, p_nome_profilo, p_competenza);

    -- Controllo che l'utente NON sia il creatore del progetto
    IF EXISTS (SELECT 1
               FROM PROGETTO
               WHERE nome = p_nome_progetto
                 AND email_creatore = p_email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: UTENTE CREATORE DEL PROGETTO';
    END IF;

    -- Controllo che l'utente non abbia già una candidatura per quella competenza
    IF EXISTS (SELECT 1
               FROM PARTECIPANTE
               WHERE email_utente = p_email
                 AND nome_progetto = p_nome_progetto
                 AND nome_profilo = p_nome_profilo
                 AND competenza = p_competenza) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: CANDIDATURA INSERITA PRECEDENTEMENTE';
    END IF;
END//

-- COMMENTO: sp_commento_check
--  sp_commento_insert (sp_commento_check)
--  sp_commento_delete (sp_commento_check)
--  sp_commento_risposta_insert (sp_commento_risposta_check)
--  sp_commento_risposta_delete (sp_commento_risposta_check)

-- Controlli generici per tutte le stored procedure necessari per commento e risposta
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
                SET MESSAGE_TEXT = 'ERRORE: NON SEI AUTORIZZATO A CANCELLARE QUESTO COMMENTO';
        END IF;
    END IF;
END//

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
                SET MESSAGE_TEXT = 'ERRORE: COMMENTO CONTIENE RISPOSTA';
        END IF;
    ELSE
        -- Altrimenti si intende cancellare una risposta...
        IF NOT ( -- L'utente deve essere il creatore del progetto o un admin
            EXISTS (SELECT 1 FROM PROGETTO WHERE nome = p_nome_progetto AND email_creatore = p_email_creatore)
                OR
            EXISTS (SELECT 1 FROM ADMIN WHERE email_utente = p_email_creatore)
            ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'ERRORE: UTENTE NON CREATORE O ADMIN (CANCELLAZIONE RISPOSTA)';
        END IF;

        -- Controllo che il commento ABBIA una risposta da cancellare
        IF EXISTS (SELECT 1 FROM COMMENTO WHERE id = p_commento_id AND risposta IS NULL) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'ERRORE: COMMENTO NON CONTIENE RISPOSTA';
        END IF;
    END IF;
END//

-- FOTO: sp_foto_check
--  sp_foto_insert
--  sp_foto_delete

CREATE PROCEDURE sp_foto_check(
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_foto_id INT,
    IN p_is_insert BOOLEAN
)
BEGIN
    -- Controllo che l'utente sia il creatore del progetto
    CALL sp_util_is_creatore_progetto_owner(p_email_creatore, p_nome_progetto);

    -- Controllo che la foto esista (solo per update e delete)
    IF NOT p_is_insert AND NOT EXISTS (SELECT 1
                                       FROM FOTO
                                       WHERE nome_progetto = p_nome_progetto
                                         AND id = p_foto_id) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: FOTO NON ESISTENTE';
    END IF;
END//

DELIMITER ;

-- ==================================================
-- STORED PROCEDURES (MAIN)
-- ==================================================

-- In questo blocco vengono definite le stored procedure principali, che implementano le funzionalità richieste dal sistema, con qualche aggiunta mia.
-- Vengono divise in base alla tabella di riferimento, con la seguente sintassi generale...

-- NOME_TABELLA:
--  sp_nome_procedura_{insert|delete|update} o altre azioni specifiche

-- Al di sopra della definizione di ogni stored procedure principale, si documenta la funzione di esse e che tipo di utente può utilizzarle (in parentesi).
-- L'interno di ogni stored procedure è diviso in tre parti:
--  1. Controllo dei parametri in input (il controllo assistito dalle stored procedure "helper")
--  2. Operazioni sul database relative

DELIMITER //

-- UTENTE:
--  sp_utente_registra
--  sp_utente_login

-- (ALL) Registrazione di un utente con o senza ruolo di creatore
CREATE PROCEDURE sp_utente_registra(
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    IN p_nickname VARCHAR(50),
    IN p_nome VARCHAR(50),
    IN p_cognome VARCHAR(50),
    IN p_anno_nascita INT,
    IN p_luogo_nascita VARCHAR(50),
    IN p_is_creatore BOOLEAN, -- Definito a livello di interfaccia (checkbox)
    IN p_is_admin BOOLEAN, -- Definito a livello di interfaccia (checkbox)
    IN p_codice_sicurezza VARCHAR(100) -- Codice di sicurezza per admin
)
BEGIN
    START TRANSACTION; -- Uso di transazione per garantire l'integrità dei dati
    INSERT INTO UTENTE (email, password, nickname, nome, cognome, anno_nascita, luogo_nascita)
    VALUES (p_email, p_password, p_nickname, p_nome, p_cognome, p_anno_nascita, p_luogo_nascita);

    IF p_is_creatore THEN
        INSERT INTO CREATORE (email_utente)
        VALUES (p_email);
    END IF;

    IF p_is_admin THEN
        IF p_codice_sicurezza IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'ERRORE: CODICE DI SICUREZZA ADMIN MANCANTE';
        END IF;
        INSERT INTO ADMIN (email_utente, codice_sicurezza)
        VALUES (p_email, p_codice_sicurezza);
    END IF;
    COMMIT;
END//

-- (ALL) Login di un utente
CREATE PROCEDURE sp_utente_login(
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    IN p_codice_sicurezza VARCHAR(100),
    OUT p_nickname_out VARCHAR(50),
    OUT p_email_out VARCHAR(100)
)
BEGIN
    START TRANSACTION;
    -- (ALL) Controllo che esista un utente con le credenziali fornite
    IF NOT EXISTS (SELECT 1
                   FROM UTENTE
                   WHERE email = p_email
                     AND password = p_password) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: CREDEZIALI NON VALIDE';
    END IF;

    -- (ADMIN) Controlla il codice di sicurezza se l'utente è un admin
    IF p_codice_sicurezza IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1
                       FROM ADMIN
                       WHERE email_utente = p_email
                         AND codice_sicurezza = p_codice_sicurezza) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'ERRORE: CODICE DI SICUREZZA ADMIN NON VALIDO';
        END IF;
    END IF;

    SELECT nickname, email
    INTO p_nickname_out, p_email_out
    FROM UTENTE
    WHERE email = p_email;
    COMMIT;
END//

-- SKILL_CURRICULUM:
--  sp_skill_curriculum_insert

-- (ALL) Inserimento di skill in un curriculum utente
CREATE PROCEDURE sp_skill_curriculum_insert(
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

-- PROGETTO:
--  sp_visualizza_progetti
--  sp_progetto_insert

-- (ALL) Visualizzazione dei progetti disponibili
CREATE PROCEDURE sp_visualizza_progetti()
BEGIN
    SELECT * FROM PROGETTO;
END//

-- (CREATORE) Inserimento di un nuovo progetto (nr_progetti incrementato via trg_incrementa_progetti_creati)
CREATE PROCEDURE sp_progetto_insert(
    IN p_nome VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_descrizione TEXT,
    IN p_budget DECIMAL(10, 2),
    IN p_data_limite DATE,
    IN p_tipo ENUM ('software','hardware') -- Tipo di progetto, definito a livello di interfaccia (checkbox)
)
BEGIN
    START TRANSACTION;
    -- Controllo che l'utente sia un creatore
    CALL sp_util_is_utente_creatore(p_email_creatore);

    -- Controllo che la data_limite sia futura alla data attuale
    IF p_data_limite <= CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: DATA LIMITE DEVE ESSERE FUTURA';
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

-- FINANZIAMENTO:
--  sp_finanziamento_insert

-- (ALL) Finanziamento di un progetto aperto da parte di un utente (anche creatore)
CREATE PROCEDURE sp_finanziamento_insert(
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_codice_reward VARCHAR(50), -- Si presuppone che il codice sia scelto dall'utente al livello di interfaccia
    IN p_importo DECIMAL(10, 2)
)
BEGIN
    START TRANSACTION;
    -- Controllo che il progetto sia aperto
    IF NOT EXISTS (SELECT 1
                   FROM PROGETTO
                   WHERE nome = p_nome_progetto
                     AND stato = 'aperto') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO CHIUSO O NON ESISTENTE';
    END IF;

    -- Se il controllo passa, inserisco il finanziamento
    INSERT INTO FINANZIAMENTO (email_utente, nome_progetto, codice_reward, importo)
    VALUES (p_email, p_nome_progetto, p_codice_reward, p_importo);
    COMMIT;
END//

-- COMMENTO:
--  sp_commento_insert
--  sp_commento_delete
--  sp_commento_risposta_insert
--  sp_commento_risposta_delete

-- (ALL) Inserimento di un commento a un progetto
CREATE PROCEDURE sp_commento_insert(
    IN p_email_autore VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_testo TEXT
)
BEGIN
    START TRANSACTION;
    -- Controllo che:
    --  1. Esista il progetto
    CALL sp_commento_check(NULL, p_nome_progetto, p_email_autore, TRUE);

    -- Se il controllo passa, inserisco il commento
    INSERT INTO COMMENTO (email_utente, nome_progetto, testo)
    VALUES (p_email_autore, p_nome_progetto, p_testo);
    COMMIT;
END//

-- (ALL) Cancellazione di un commento a un progetto
DELIMITER //
CREATE PROCEDURE sp_commento_delete(
    IN p_id INT,
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
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

-- (CREATORE) Inserimento di una risposta a un commento
CREATE PROCEDURE sp_commento_risposta_insert(
    IN p_commento_id INT,
    IN p_email_creatore VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_risposta TEXT
)
BEGIN
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

-- (CREATORE) Cancellazione di una risposta a un commento
CREATE PROCEDURE sp_commento_risposta_delete(
    IN p_commento_id INT,
    IN p_email_creatore VARCHAR(100),
    IN p_nome_progetto VARCHAR(100)
)
BEGIN
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

-- (ALL) Inserimento di una candidatura a un progetto software
CREATE PROCEDURE sp_partecipante_utente_insert(
    IN p_email VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100)
)
BEGIN
    START TRANSACTION;
    -- Controllo che:
    --  1. Il progetto sia di tipo software
    --  2. Il profilo per quel progetto esista
    --  3. L'utente non sia il creatore del progetto
    --  4. L'utente non abbia già una candidatura per quella competenza
    CALL sp_partecipante_utente_check(p_email, p_nome_progetto, p_nome_profilo, p_competenza);

    -- Se i controlli passano, inserisco la candidatura
    INSERT INTO PARTECIPANTE (email_utente, nome_progetto, nome_profilo, competenza)
    VALUES (p_email, p_nome_progetto, p_nome_profilo, p_competenza);
    COMMIT;
END//

-- (CREATORE) Accettazione o rifiuto di una candidatura a un progetto
CREATE PROCEDURE sp_partecipante_creatore_update(
    IN p_email_creatore VARCHAR(100),
    IN p_email_candidato VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100),
    IN p_nuovo_stato ENUM ('accettato','rifiutato')
)
BEGIN
    START TRANSACTION;
    -- Controllo che:
    --  1. Il progetto sia di tipo software
    --  2. Il profilo per quel progetto esista
    --  3. L'utente sia il creatore del progetto
    --  4. La candidatura esista
    CALL sp_partecipante_creatore_check(p_email_creatore, p_email_candidato, p_nome_progetto, p_nome_profilo,
                                        p_competenza);

    -- Se i controlli passano, aggiorno lo stato della candidatura
    UPDATE PARTECIPANTE
    SET stato = p_nuovo_stato
    WHERE email_utente = p_email_candidato
      AND nome_progetto = p_nome_progetto
      AND nome_profilo = p_nome_profilo
      AND competenza = p_competenza;
    COMMIT;
END//

-- SKILL:
--  sp_skill_insert

-- (ADMIN) Inserimento di una nuova stringa nella lista delle competenze
CREATE PROCEDURE sp_skill_insert(
    IN p_competenza VARCHAR(100),
    IN p_email VARCHAR(100)
)
BEGIN
    START TRANSACTION;
    -- Controllo che l'admin sia l'utente che esegue la procedura
    CALL sp_util_is_utente_admin(p_email);

    -- Se il controllo passa, inserisco la competenza
    INSERT INTO SKILL (competenza)
    VALUES (p_competenza);
    COMMIT;
END//

-- REWARD:
--  sp_reward_insert

-- (CREATORE) Inserimento di una reward per un progetto
CREATE PROCEDURE sp_reward_insert(
    IN p_codice VARCHAR(50),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_descrizione TEXT,
    IN p_foto MEDIUMBLOB,
    IN p_min_importo DECIMAL(10, 2)
)
BEGIN
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

-- COMPONENTE:
--  sp_componente_insert
--  sp_componente_delete
--  sp_componente_update

-- (CREATOR) Inserimento di un componente per un progetto hardware
-- trg_aggiorna_budget_componente_insert si occupa di aggiornare il budget del progetto
CREATE PROCEDURE sp_componente_insert(
    IN p_nome_componente VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_descrizione TEXT,
    IN p_quantita INT,
    IN p_prezzo DECIMAL(10, 2),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- (CREATOR) Rimozione di un componente per un progetto hardware
-- trg_aggiorna_budget_componente_delete si occupa di aggiornare il budget del progetto
CREATE PROCEDURE sp_componente_delete(
    IN p_nome_componente VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- (CREATOR) Aggiornamento di un componente per un progetto hardware
-- trg_aggiorna_budget_componente_update si occupa di aggiornare il budget del progetto
CREATE PROCEDURE sp_componente_update(
    IN p_nome_componente VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_descrizione TEXT,
    IN p_quantita INT,
    IN p_prezzo DECIMAL(10, 2),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- PROFILO:
--  sp_profilo_insert
--  sp_profilo_delete

-- (CREATOR) Inserimento di un profilo per un progetto
CREATE PROCEDURE sp_profilo_insert(
    IN p_nome_profilo VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- (CREATOR) Rimozione di un profilo per un progetto
CREATE PROCEDURE sp_profilo_delete(
    IN p_nome_profilo VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- SKILL_PROFILO:
--  sp_skill_profilo_insert
--  sp_skill_profilo_delete
--  sp_skill_profilo_update

-- (CREATOR) Inserimento di una skill per un profilo di un progetto
CREATE PROCEDURE sp_skill_profilo_insert(
    IN p_nome_profilo VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_competenza VARCHAR(100),
    IN p_livello_richiesto TINYINT
)
BEGIN
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

-- (CREATOR) Rimozione di una skill per un profilo di un progetto
CREATE PROCEDURE sp_skill_profilo_delete(
    IN p_nome_profilo VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_competenza VARCHAR(100),
    IN p_email_creatore VARCHAR(100)
)
BEGIN
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

-- (CREATOR) Aggiornamento di una skill per un profilo di un progetto
CREATE PROCEDURE sp_skill_profilo_update(
    IN p_nome_profilo VARCHAR(100),
    IN p_competenza VARCHAR(100),
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_nuovo_livello_richiesto TINYINT
)
BEGIN
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

-- (CREATOR) Aggiunta di una foto a un progetto
CREATE PROCEDURE sp_foto_insert(
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_foto_id INT,
    IN p_foto MEDIUMBLOB
)
BEGIN
    START TRANSACTION;
    -- Controllo che:
    --  1. L'utente sia il creatore del progetto
    CALL sp_foto_check(p_nome_progetto, p_email_creatore, p_foto_id, TRUE);

    -- Se il controllo passa, aggiungo la foto
    INSERT INTO FOTO (nome_progetto, id, foto)
    VALUES (p_nome_progetto, p_foto_id, p_foto);
    COMMIT;
END//

-- (CREATOR) Rimozione di una foto a un progetto
CREATE PROCEDURE sp_foto_delete(
    IN p_nome_progetto VARCHAR(100),
    IN p_email_creatore VARCHAR(100),
    IN p_foto_id INT
)
BEGIN
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
      AND P.stato = 'potenziale'
      AND P.nome_progetto IN (SELECT nome_progetto
                              FROM SKILL_PROFILO
                              WHERE nome_profilo = NEW.nome_profilo
                                AND competenza = NEW.competenza)
      AND NOT EXISTS (SELECT 1
                      FROM SKILL_CURRICULUM SC
                      WHERE SC.email_utente = P.email_utente
                        AND SC.competenza = NEW.competenza
                        AND SC.livello_effettivo >= NEW.livello_richiesto);
END//

-- Trigger per rifiutare automaticamente candidature se il livello_effettivo non è sufficiente rispetto al livello_richiesto
CREATE TRIGGER trg_rifiuta_candidatura_livello_effettivo_insufficiente
    BEFORE INSERT
    ON PARTECIPANTE
    FOR EACH ROW
BEGIN
    -- Controllo che il progetto sia ancora aperto
    IF (SELECT stato FROM PROGETTO WHERE nome = NEW.nome_progetto) = 'chiuso' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: PROGETTO CHIUSO';
    END IF;

    -- Controllo che il livello effettivo sia sufficiente per il profilo richiesto
    IF EXISTS (SELECT 1
               FROM SKILL_PROFILO SP
                        LEFT JOIN SKILL_CURRICULUM SC -- LEFT JOIN così se l'utente non ha la skill, il livello effettivo è NULL e viene considerato insufficiente
                                  ON SP.competenza = SC.competenza AND SC.email_utente = NEW.email_utente
               WHERE SP.nome_profilo = NEW.nome_profilo
                   AND SC.livello_effettivo IS NULL
                  OR SC.livello_effettivo < SP.livello_richiesto) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERRORE: LIVELLO EFFETTIVO NON SUFFICIENTE PER IL PROFILO';
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
          AND stato = 'potenziale'
          AND nome_progetto IN (SELECT nome FROM PROGETTO WHERE stato = 'chiuso');
    END IF;
END//

-- Trigger per rimuovere automaticamente le candidature se il profilo viene rimosso dal progetto
CREATE TRIGGER trg_delete_candidature_profilo_rimosso_da_progetto
    AFTER DELETE
    ON PROFILO
    FOR EACH ROW
BEGIN
    DELETE
    FROM PARTECIPANTE
    WHERE nome_profilo = OLD.nome_profilo
      AND nome_progetto = OLD.nome_progetto;
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