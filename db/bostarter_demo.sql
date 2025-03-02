-- ==================================================
--                  BOSTARTER DEMO
-- ==================================================

USE BOSTARTER;

-- ==================================================
-- UTENTE REGISTRATION (ALL)
-- ==================================================

-- ADMIN
CALL sp_utente_register('alice@example.com', '$2y$10$iyV4M0QFH/6jRfhpLQi7o.sQY4psXIIgu0nKcXy9JomlEzUP5OkJG', 'alice',
                        'Alice', 'Rossi', 1980, 'Milano', FALSE);
INSERT INTO ADMIN (email_utente, codice_sicurezza)
VALUES ('alice@example.com', 'admincode123');
CALL sp_utente_register('admin2@example.com', '$2y$10$38i/9cY.ZBGsUVHTyOvX6.BnhaDbDFl.tdGhXmaagEG6.tp8bNccG',
                        'adminovich',
                        'Admin', 'Adminovich', 1975, 'Milano', FALSE);
INSERT INTO ADMIN (email_utente, codice_sicurezza)
VALUES ('admin2@example.com', 'admincode456');

-- CREATORE & UTENTE
CALL sp_utente_register('bob@example.com', '$2y$10$UD3Szzw46Z.YGUVRwnbknOHa4pxJJZin/bd3E50DmeHID1NF/zkvC', 'bob', 'Bob',
                        'Bianchi', 1985, 'Roma', TRUE);
CALL sp_utente_register('charlie@example.com', '$2y$10$MTrRjFubLx0pFikUw9f3q.JUI114hSzHiw5QTEshcmkMb7QFmCpfe',
                        'charlie', 'Charlie', 'Verdi', 1992, 'Napoli', FALSE);
CALL sp_utente_register('diana@example.com', '$2y$10$ekIyDsnc1kxUgYmwggpkxOszcnWxzs3Ar/VQJXLcwc8AVt7l5aCZm', 'diana',
                        'Diana', 'Neri', 1988, 'Torino', TRUE);
CALL sp_utente_register('eric@example.com', '$2y$10$wPdkIxc6GqZtd4DeK6LShOtrkKrjlP11.rgpCghedWvSnw.o2qxNK', 'eric',
                        'Eric', 'Gialli', 1995, 'Firenze', FALSE);
CALL sp_utente_register('frank@example.com', '$2y$10$7iuz26zxyC/S51dbXsrvQuMQc5BPVkfhWENR.H1vExD29wgJVcGqC', 'frank',
                        'Frank', 'Marrone', 1990, 'Bologna', FALSE);
CALL sp_utente_register('grace@example.com', '$2y$10$cOYdjYD0gIgu8gVOO.//euBHFBTR/39nJpdIHuoebEjyYC1UfFCry', 'grace',
                        'Grace', 'Verdi', 1987, 'Palermo', TRUE);
CALL sp_utente_register('heidi@example.com', '$2y$10$Nk.mtGOdrDa1OQAkeMtN9.loPlu.ufqdQTJd8yhIqOvWSjorAPjaS', 'heidi',
                        'Heidi', 'Blu', 1993, 'Genova', FALSE);
CALL sp_utente_register('ivan@example.com', '$2y$10$MLGsQC2YpvzvQzjRHaDEseRRAMwZ3xb6CyuKzBySQIZCVeO0LDNT6', 'ivan',
                        'Ivan',
                        'Grosso', 1984, 'Verona', TRUE);
CALL sp_utente_register('judy@example.com', '$2y$10$bdagVYwY2G9RtsIkhRzBHOvmoWQbWAWS.J9.4YpAyrfnm80bhiMYO', 'judy',
                        'Judy',
                        'Rossi', 1991, 'Bari', FALSE);
CALL sp_utente_register('karen@example.com', '$2y$10$ea9U5jWa3md4aSA3cpvIO.3DgUjbqXpa.rSWtfnSv/kX20FDNEvIC', 'karen',
                        'Karen', 'Neri', 1986, 'Catania', TRUE);
CALL sp_utente_register('leo@example.com', '$2y$10$V9F.HfDtgABiHFPL.okctuZLFUKa1oBfbxzfKkS42b7wwICQnlmdC', 'leo', 'Leo',
                        'Verdi', 1994, 'Messina', FALSE);
CALL sp_utente_register('mike@example.com', '$2y$10$/9YpR7i31T7.1bERl7sG2eGI6IqmnBwEtpGMcyTBHDSGS7QNSFBIa', 'mike',
                        'Mike',
                        'Gialli', 1989, 'Padova', FALSE);
CALL sp_utente_register('nancy@example.com', '$2y$10$o7NCQWVeMh/Q/fbd19aSKuae4a84G6PX2CGvfViUX2wrDYXnWiN46', 'nancy',
                        'Nancy', 'Blu', 1983, 'Trieste', TRUE);
CALL sp_utente_register('oscar@example.com', '$2y$10$DLJ4Schqy7b6L9Zb3vgt9unrscONRX0BF4TQqaCXYVm9S/oO0i5KW', 'oscar',
                        'Oscar', 'Marrone', 1990, 'Livorno', FALSE);

-- ==================================================
-- SKILL INSERTION (ADMIN)
-- ==================================================
CALL sp_skill_insert('Java', 'alice@example.com');
CALL sp_skill_insert('PHP', 'alice@example.com');
CALL sp_skill_insert('JavaScript', 'alice@example.com');
CALL sp_skill_insert('CSS', 'alice@example.com');
CALL sp_skill_insert('MySQL', 'alice@example.com');
CALL sp_skill_insert('Python', 'alice@example.com');
CALL sp_skill_insert('C++', 'alice@example.com');
CALL sp_skill_insert('Ruby', 'alice@example.com');
CALL sp_skill_insert('SQL', 'alice@example.com');
CALL sp_skill_insert('HTML', 'alice@example.com');
CALL sp_skill_insert('React', 'alice@example.com');
CALL sp_skill_insert('Angular', 'alice@example.com');
CALL sp_skill_insert('Node.js', 'alice@example.com');
CALL sp_skill_insert('Django', 'alice@example.com');
CALL sp_skill_insert('Flask', 'alice@example.com');

-- ==================================================
-- SKILL_CURRICULUM INSERTION (ALL)
-- ==================================================
CALL sp_skill_curriculum_insert('alice@example.com', 'PHP', 5);
CALL sp_skill_curriculum_insert('alice@example.com', 'MySQL', 4);
CALL sp_skill_curriculum_insert('bob@example.com', 'JavaScript', 3);
CALL sp_skill_curriculum_insert('charlie@example.com', 'CSS', 4);
CALL sp_skill_curriculum_insert('charlie@example.com', 'JavaScript', 5);
CALL sp_skill_curriculum_insert('diana@example.com', 'PHP', 4);
CALL sp_skill_curriculum_insert('eric@example.com', 'JavaScript', 4);
CALL sp_skill_curriculum_insert('eric@example.com', 'PHP', 5);
CALL sp_skill_curriculum_insert('frank@example.com', 'Python', 4);
CALL sp_skill_curriculum_insert('grace@example.com', 'JavaScript', 5);
CALL sp_skill_curriculum_insert('heidi@example.com', 'HTML', 3);
CALL sp_skill_curriculum_insert('ivan@example.com', 'C++', 4);
CALL sp_skill_curriculum_insert('judy@example.com', 'SQL', 3);
CALL sp_skill_curriculum_insert('judy@example.com', 'Python', 4);
CALL sp_skill_curriculum_insert('karen@example.com', 'Ruby', 4);
CALL sp_skill_curriculum_insert('leo@example.com', 'React', 3);
CALL sp_skill_curriculum_insert('mike@example.com', 'Angular', 4);
CALL sp_skill_curriculum_insert('mike@example.com', 'CSS', 5);
CALL sp_skill_curriculum_insert('nancy@example.com', 'Node.js', 5);
CALL sp_skill_curriculum_insert('oscar@example.com', 'Django', 4);
CALL sp_skill_curriculum_insert('oscar@example.com', 'SQL', 5);

-- ==================================================
-- PROGETTO INSERTION (CREATORE)
-- ==================================================
CALL sp_progetto_insert('ProgettoAlpha', 'bob@example.com', 'Software project Alpha description', 10000.00,
                        '2025-12-31', 'software');
CALL sp_progetto_insert('ProgettoBeta', 'diana@example.com', 'Hardware project Beta description', 5000.00, '2025-11-30',
                        'hardware');
CALL sp_progetto_insert('ProgettoGamma', 'grace@example.com', 'Software project Gamma description', 15000.00,
                        '2026-01-31', 'software');
CALL sp_progetto_insert('ProgettoDelta', 'ivan@example.com', 'Hardware project Delta description', 8000.00,
                        '2026-03-31', 'hardware');
CALL sp_progetto_insert('ProgettoEpsilon', 'karen@example.com', 'Software project Epsilon description', 12000.00,
                        '2026-05-31', 'software');
CALL sp_progetto_insert('ProgettoKappa', 'ivan@example.com', 'Hardware project Kappa description', 7500.00,
                        '2027-03-31', 'hardware');

-- ==================================================
-- REWARD INSERTION (CREATORE)
-- ==================================================
CALL sp_reward_insert('RWD1', 'ProgettoAlpha', 'bob@example.com', 'Reward for early supporters of Alpha', x'010203',
                      100.00);
CALL sp_reward_insert('RWD2', 'ProgettoBeta', 'diana@example.com', 'Reward for hardware supporters of Beta', x'040506',
                      200.00);
CALL sp_reward_insert('RWD3', 'ProgettoGamma', 'grace@example.com', 'Reward for early supporters of Gamma', x'070809',
                      150.00);
CALL sp_reward_insert('RWD4', 'ProgettoDelta', 'ivan@example.com', 'Reward for hardware supporters of Delta', x'0A0B0C',
                      180.00);
CALL sp_reward_insert('RWD5', 'ProgettoEpsilon', 'karen@example.com', 'Reward for early supporters of Epsilon',
                      x'0D0E0F', 130.00);
CALL sp_reward_insert('RWD10', 'ProgettoKappa', 'ivan@example.com', 'Reward for hardware supporters of Kappa',
                      x'1C1D1E', 150.00);

-- ==================================================
-- COMPONENTE OPERATIONS (CREATORE)
-- ==================================================
-- For ProgettoBeta:
CALL sp_componente_insert('Comp1', 'ProgettoBeta', 'High quality screws', 100, 0.50, 'diana@example.com');
CALL sp_componente_insert('Comp2', 'ProgettoBeta', 'Circuit boards', 10, 150.00, 'diana@example.com');
CALL sp_componente_insert('Comp3', 'ProgettoBeta', 'High quality LEDs', 50, 1.00, 'diana@example.com');
CALL sp_componente_update('Comp1', 'ProgettoBeta', 'High quality screws - updated', 120, 0.50, 'diana@example.com');
CALL sp_componente_delete('Comp2', 'ProgettoBeta', 'diana@example.com');

-- For ProgettoDelta:
CALL sp_componente_insert('Comp4', 'ProgettoDelta', 'Durable metal casing', 30, 200.00, 'ivan@example.com');
CALL sp_componente_insert('Comp5', 'ProgettoDelta', 'High precision sensors', 15, 350.00, 'ivan@example.com');

-- For ProgettoKappa:
CALL sp_componente_insert('Comp8', 'ProgettoKappa', 'Ergonomic design parts', 50, 25.00, 'ivan@example.com');
CALL sp_componente_insert('Comp9', 'ProgettoKappa', 'Precision machined components', 20, 75.00, 'ivan@example.com');

-- ==================================================
-- PROFILO & SKILL_PROFILO OPERATIONS (CREATORE)
-- ==================================================
-- For ProgettoAlpha:
CALL sp_profilo_insert('Developer', 'ProgettoAlpha', 'bob@example.com');
CALL sp_skill_profilo_insert('Developer', 'ProgettoAlpha', 'bob@example.com', 'JavaScript', 3);

-- For ProgettoGamma:
CALL sp_profilo_insert('Developer', 'ProgettoGamma', 'grace@example.com');
CALL sp_skill_profilo_insert('Developer', 'ProgettoGamma', 'grace@example.com', 'Python', 3);

-- For ProgettoEpsilon:
CALL sp_profilo_insert('Designer', 'ProgettoEpsilon', 'karen@example.com');
CALL sp_skill_profilo_insert('Designer', 'ProgettoEpsilon', 'karen@example.com', 'CSS', 4);

-- ==================================================
-- COMMENTO OPERATIONS (ALL)
-- ==================================================

-- For ProgettoAlpha:
CALL sp_commento_insert('heidi@example.com', 'ProgettoAlpha', 'I love the concept of Alpha!');
CALL sp_commento_insert('mike@example.com', 'ProgettoAlpha', 'Great work on Alpha!');
CALL sp_commento_insert('oscar@example.com', 'ProgettoAlpha', 'Alpha looks promising!');
CALL sp_commento_risposta_insert(1, 'bob@example.com', 'ProgettoAlpha', 'Great to hear that, Heidi!');
CALL sp_commento_risposta_insert(2, 'bob@example.com', 'ProgettoAlpha', 'Thanks, Mike!');

CALL sp_commento_insert('judy@example.com', 'ProgettoGamma', 'Gamma is revolutionary!');
CALL sp_commento_insert('leo@example.com', 'ProgettoDelta', 'Delta seems robust and innovative.');
CALL sp_commento_insert('charlie@example.com', 'ProgettoKappa', 'Kappa looks solid and well-planned.');

-- ==================================================
-- PARTECIPANTE OPERATIONS (ALL)
-- ==================================================

-- For ProgettoAlpha:
CALL sp_partecipante_utente_insert('eric@example.com', 'ProgettoAlpha', 'Developer', 'JavaScript');
CALL sp_partecipante_creatore_update('bob@example.com', 'eric@example.com', 'ProgettoAlpha', 'Developer', 'JavaScript',
                                     'accettato');

-- For ProgettoGamma:
CALL sp_partecipante_utente_insert('judy@example.com', 'ProgettoGamma', 'Developer', 'Python');
CALL sp_partecipante_creatore_update('grace@example.com', 'judy@example.com', 'ProgettoGamma', 'Developer', 'Python',
                                     'accettato');

-- For ProgettoEpsilon:
CALL sp_partecipante_utente_insert('mike@example.com', 'ProgettoEpsilon', 'Designer', 'CSS');
CALL sp_partecipante_creatore_update('karen@example.com', 'mike@example.com', 'ProgettoEpsilon', 'Designer', 'CSS',
                                     'accettato');

-- ==================================================
-- FINANZIAMENTO OPERATIONS (ALL)
-- ==================================================
CALL sp_finanziamento_insert('charlie@example.com', 'ProgettoAlpha', 'RWD1', 150.00);
CALL sp_finanziamento_insert('eric@example.com', 'ProgettoBeta', 'RWD2', 250.00);
CALL sp_finanziamento_insert('diana@example.com', 'ProgettoAlpha', 'RWD1', 200.00);
CALL sp_finanziamento_insert('frank@example.com', 'ProgettoGamma', 'RWD3', 300.00);
CALL sp_finanziamento_insert('heidi@example.com', 'ProgettoDelta', 'RWD4', 350.00);
CALL sp_finanziamento_insert('leo@example.com', 'ProgettoEpsilon', 'RWD5', 400.00);
CALL sp_finanziamento_insert('nancy@example.com', 'ProgettoKappa', 'RWD10', 600.00);