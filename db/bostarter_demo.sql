-- ==================================================
--                  BOSTARTER DEMO
-- ==================================================

USE BOSTARTER;

-- ==================================================
-- UTENTE REGISTRATION (ALL)
-- ==================================================
/*
   - Alice: ADMIN
   - Bob e Diana: CREATORE
   - Charlie e Eric: UTENTE

    Le password sono hashed, ma in plaintext sono: pass{Nome} dove {Nome} è il nome dell'utente.

    Utenti amministratori, essendo privilegiati, non possono essere registrati tramite la procedura sp_utente_register (usata per utenti normali).
    Si presuppone che gli amministratori siano poco numerosi e vengano inseriti manualmente per motivi di sicurezza.
*/
CALL sp_utente_register('alice@example.com', '$2y$10$iyV4M0QFH/6jRfhpLQi7o.sQY4psXIIgu0nKcXy9JomlEzUP5OkJG', 'alice', 'Alice', 'Rossi', 1980, 'Milano', FALSE);
INSERT INTO ADMIN (email_utente, codice_sicurezza) VALUES ('alice@example.com', 'admincode123');

CALL sp_utente_register('bob@example.com', '$2y$10$UD3Szzw46Z.YGUVRwnbknOHa4pxJJZin/bd3E50DmeHID1NF/zkvC', 'bob', 'Bob', 'Bianchi', 1985, 'Roma', TRUE);
CALL sp_utente_register('charlie@example.com', '$2y$10$MTrRjFubLx0pFikUw9f3q.JUI114hSzHiw5QTEshcmkMb7QFmCpfe', 'charlie', 'Charlie', 'Verdi', 1992, 'Napoli', FALSE);
CALL sp_utente_register('diana@example.com', '$2y$10$ekIyDsnc1kxUgYmwggpkxOszcnWxzs3Ar/VQJXLcwc8AVt7l5aCZm', 'diana', 'Diana', 'Neri', 1988, 'Torino', TRUE);
CALL sp_utente_register('eric@example.com', '$2y$10$wPdkIxc6GqZtd4DeK6LShOtrkKrjlP11.rgpCghedWvSnw.o2qxNK', 'eric', 'Eric', 'Gialli', 1995, 'Firenze', FALSE);

-- ==================================================
-- SKILL INSERTION (ADMIN)
-- ==================================================
/*
   Admin (Alice) inserisce delle skill nel sistema a livello globale.
   Il parametro p_email serve per verificare che solo l'admin possa inserire skill,
   il suo valore non è utilizzato per l'inserimento effettivo, e non è inserito come argomento dal client.
*/
CALL sp_skill_insert('Java', 'alice@example.com');
CALL sp_skill_insert('PHP', 'alice@example.com');
CALL sp_skill_insert('JavaScript', 'alice@example.com');
CALL sp_skill_insert('CSS', 'alice@example.com');
CALL sp_skill_insert('MySQL', 'alice@example.com');

-- ==================================================
-- SKILL_CURRICULUM (ALL)
-- ==================================================
/*
    Inserimento di skill nel curriculum di alcuni utenti:
      - Alice: PHP, MySQL
      - Bob: JavaScript
      - Charlie: CSS
      - Diana: PHP
      - Eric: JavaScript, PHP
*/
CALL sp_skill_curriculum_insert('alice@example.com', 'PHP', 5);
CALL sp_skill_curriculum_insert('alice@example.com', 'MySQL', 4);
CALL sp_skill_curriculum_insert('bob@example.com', 'JavaScript', 3);
CALL sp_skill_curriculum_insert('charlie@example.com', 'CSS', 4);
CALL sp_skill_curriculum_insert('diana@example.com', 'PHP', 4);
CALL sp_skill_curriculum_insert('eric@example.com', 'JavaScript', 4);
CALL sp_skill_curriculum_insert('eric@example.com', 'PHP', 5);

-- ==================================================
-- PROGETTO INSERTION (CREATORE)
-- ==================================================
/*
   - "ProgettoAlpha": software project gestito da Bob
   - "ProgettoBeta": hardware project gestito da Diana
*/
CALL sp_progetto_insert('ProgettoAlpha', 'bob@example.com', 'Software project Alpha description', 10000.00, '2025-12-31', 'software');
CALL sp_progetto_insert('ProgettoBeta', 'diana@example.com', 'Hardware project Beta description', 5000.00, '2025-11-30', 'hardware');

-- ==================================================
-- REWARD INSERTION (CREATORE)
-- ==================================================
/*
   Insert rewards for projects:
     - "ProgettoAlpha" by Bob
     - "ProgettoBeta" by Diana
*/
CALL sp_reward_insert('RWD1', 'ProgettoAlpha', 'bob@example.com', 'Reward per early supporters', x'010203', 100.00);
CALL sp_reward_insert('RWD2', 'ProgettoBeta', 'diana@example.com', 'Reward per hardware supporters', x'040506', 200.00);

-- ==================================================
-- COMPONENTE OPERATIONS (CREATORE)
-- ==================================================
/*
   Per progetto hardware "ProgettoBeta" (di Diana):
     - Insert three components
     - Update one component
     - Delete one component
*/
CALL sp_componente_insert('Comp1', 'ProgettoBeta', 'High quality screws', 100, 0.50, 'diana@example.com');
CALL sp_componente_insert('Comp2', 'ProgettoBeta', 'Circuit boards', 10, 150.00, 'diana@example.com');
CALL sp_componente_insert('Comp3', 'ProgettoBeta', 'High quality LEDs', 50, 1.00, 'diana@example.com');

-- Update component "Comp1": change description and quantity.
CALL sp_componente_update('Comp1', 'ProgettoBeta', 'High quality screws - updated', 120, 0.50, 'diana@example.com');

-- Delete component "Comp2".
CALL sp_componente_delete('Comp2', 'ProgettoBeta', 'diana@example.com');

-- ==================================================
-- PROFILO & SKILL_PROFILO OPERATIONS (CREATORE)
-- ==================================================
/*
   Per progetto software "ProgettoAlpha" (di Bob):
     - Insert a new profile "Developer" using sp_profilo_insert
     - Insert a required skill into that profile using sp_skill_profilo_insert
     - Update the required level using sp_skill_profilo_update
     - Delete the required skill using sp_skill_profilo_delete
*/
CALL sp_profilo_insert('Developer', 'ProgettoAlpha', 'bob@example.com');

CALL sp_skill_profilo_insert('Developer', 'ProgettoAlpha', 'bob@example.com', 'PHP', 3);

-- Update required level for "PHP" from 3 to 5.
CALL sp_skill_profilo_update('Developer', 'PHP', 'ProgettoAlpha', 'bob@example.com', 5);

-- Delete the "PHP" requirement from the "Developer" profile.
CALL sp_skill_profilo_delete('Developer', 'ProgettoAlpha', 'PHP', 'bob@example.com');

-- ==================================================
-- COMMENTO OPERATIONS (ALL)
-- ==================================================
/*
   Test comment operations per "ProgettoAlpha".
*/
-- Inserisci commenti su ProgettoAlpha
CALL sp_commento_insert('charlie@example.com', 'ProgettoAlpha', 'Progetto interessante!');
CALL sp_commento_insert('diana@example.com', 'ProgettoAlpha', 'WOW! Complimenti!');
CALL sp_commento_insert('eric@example.com', 'ProgettoAlpha', 'Buon lavoro Bob!');

-- Elimina il commento di Diana su ProgettoAlpha
CALL sp_commento_delete(2, 'diana@example.com', 'ProgettoAlpha');

-- Il creatore del progetto (Bob) risponde a commenti
CALL sp_commento_risposta_insert(1, 'bob@example.com', 'ProgettoAlpha', 'Grazie Charlie!');
CALL sp_commento_risposta_insert(3, 'bob@example.com', 'ProgettoAlpha', 'Grazie Eric!');

-- Elimina la risposta di Bob al commento di Charlie
CALL sp_commento_risposta_delete(1, 'bob@example.com', 'ProgettoAlpha');

-- ==================================================
-- PARTECIPANTE OPERATIONS (ALL)
-- ==================================================
/*
   Per il progetto software "ProgettoAlpha":
     - Bob (creatore) inserisce un profilo "Developer".
     - Utente Eric si candida per il profilo "Developer" su ProgettoAlpha per skill "PHP".
     - Come creatore del progetto (Bob), accetta la candidatura di Eric.
*/
-- Bob (creatore) inserisce un profilo "Developer" per ProgettoAlpha.
CALL sp_skill_profilo_insert('Developer', 'ProgettoAlpha', 'bob@example.com', 'PHP', 5);

-- Eric si candida per il profilo "Developer" su ProgettoAlpha.
CALL sp_partecipante_utente_insert('eric@example.com', 'ProgettoAlpha', 'Developer', 'PHP');

-- Bob accetta la candidatura di Eric per il profilo "Developer" su ProgettoAlpha.
CALL sp_partecipante_creatore_update('bob@example.com', 'eric@example.com', 'ProgettoAlpha', 'Developer', 'PHP', 'accettato');

-- ==================================================
-- FINANZIAMENTO OPERATIONS (ALL)
-- ==================================================
/*
   Test di inserimento di finanziamenti:
     - Charlie finanzia ProgettoAlpha
     - Eric finanzia ProgettoBeta
*/
CALL sp_finanziamento_insert('charlie@example.com', 'ProgettoAlpha', 'RWD1', 150.00);
CALL sp_finanziamento_insert('eric@example.com', 'ProgettoBeta', 'RWD2', 250.00);