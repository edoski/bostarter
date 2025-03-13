-- ==================================================
--                  BOSTARTER DEMO
-- ==================================================

USE BOSTARTER;

-- ==================================================
-- UTENTE REGISTRATION (ALL)
-- ==================================================

-- ADMIN
CALL sp_utente_register(
        'alice@example.com',
        '$2y$10$iyV4M0QFH/6jRfhpLQi7o.sQY4psXIIgu0nKcXy9JomlEzUP5OkJG',
        'alice09',
        'Alice',
        'Rossi',
        1980,
        'Milano',
        FALSE,
        TRUE,
        '$2y$10$kmjbfFVXOTs1fbLHL/yLYeWcunWL0e8aFNuGrzTa08KHdf6M.pJcO'
     );

CALL sp_utente_register(
        'admin2@example.com',
        '$2y$10$38i/9cY.ZBGsUVHTyOvX6.BnhaDbDFl.tdGhXmaagEG6.tp8bNccG',
        'adminovich',
        'Admin',
        'Adminovich',
        1975,
        'Milano',
        FALSE,
        TRUE,
        '$2y$10$hVq/oeDa4h5TuQfzAw70A.ILP8ZlQm1q/IvHZZRNff./G7EGKipqC'
     );

-- CREATORE & UTENTE
CALL sp_utente_register(
        'bob@example.com',
        '$2y$10$UD3Szzw46Z.YGUVRwnbknOHa4pxJJZin/bd3E50DmeHID1NF/zkvC',
        'bobTheBuilder',
        'Bob',
        'Bianchi',
        1985,
        'Roma',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'charlie@example.com',
        '$2y$10$MTrRjFubLx0pFikUw9f3q.JUI114hSzHiw5QTEshcmkMb7QFmCpfe',
        'charlie_chaplin',
        'Charlie',
        'Verdi',
        1992,
        'Napoli',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'diana@example.com',
        '$2y$10$ekIyDsnc1kxUgYmwggpkxOszcnWxzs3Ar/VQJXLcwc8AVt7l5aCZm',
        'diana12',
        'Diana',
        'Neri',
        1988,
        'Torino',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'edoardo.galli3@studio.unibo.it',
        '$2y$10$VbCzsqRQAgwRP5x12zH7uO801YQGG6CbZB6HRoGEHRF5vKSq2RNzi',
        'edo_unibo',
        'Edoardo',
        'Galli',
        2003,
        'Bologna',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'frank@example.com',
        '$2y$10$7iuz26zxyC/S51dbXsrvQuMQc5BPVkfhWENR.H1vExD29wgJVcGqC',
        'frankenstein',
        'Frank',
        'Marrone',
        1990,
        'Bologna',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'grace@example.com',
        '$2y$10$cOYdjYD0gIgu8gVOO.//euBHFBTR/39nJpdIHuoebEjyYC1UfFCry',
        'gracieee',
        'Grace',
        'Verdi',
        1987,
        'Palermo',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'heidi@example.com',
        '$2y$10$Nk.mtGOdrDa1OQAkeMtN9.loPlu.ufqdQTJd8yhIqOvWSjorAPjaS',
        'heidi_90',
        'Heidi',
        'Blu',
        1993,
        'Genova',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'ivan@example.com',
        '$2y$10$MLGsQC2YpvzvQzjRHaDEseRRAMwZ3xb6CyuKzBySQIZCVeO0LDNT6',
        'ivanTheTerrible',
        'Ivan',
        'Grosso',
        1984,
        'Verona',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'judy@example.com',
        '$2y$10$bdagVYwY2G9RtsIkhRzBHOvmoWQbWAWS.J9.4YpAyrfnm80bhiMYO',
        'judge_judy',
        'Judy',
        'Rossi',
        1991,
        'Bari',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'karen@example.com',
        '$2y$10$ea9U5jWa3md4aSA3cpvIO.3DgUjbqXpa.rSWtfnSv/kX20FDNEvIC',
        'karen',
        'Karen',
        'Neri',
        1986,
        'Catania',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'leo@example.com',
        '$2y$10$V9F.HfDtgABiHFPL.okctuZLFUKa1oBfbxzfKkS42b7wwICQnlmdC',
        'leo_da_vinci',
        'Leo',
        'Verdi',
        1994,
        'Messina',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'mike@example.com',
        '$2y$10$/9YpR7i31T7.1bERl7sG2eGI6IqmnBwEtpGMcyTBHDSGS7QNSFBIa',
        'mike89',
        'Mike',
        'Gialli',
        1989,
        'Padova',
        FALSE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'nancy@example.com',
        '$2y$10$o7NCQWVeMh/Q/fbd19aSKuae4a84G6PX2CGvfViUX2wrDYXnWiN46',
        'nancy',
        'Nancy',
        'Blu',
        1983,
        'Trieste',
        TRUE,
        FALSE,
        NULL
     );

CALL sp_utente_register(
        'oscar@example.com',
        '$2y$10$DLJ4Schqy7b6L9Zb3vgt9unrscONRX0BF4TQqaCXYVm9S/oO0i5KW',
        'oscar',
        'Oscar',
        'Marrone',
        1990,
        'Livorno',
        FALSE,
        FALSE,
        NULL
     );

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
CALL sp_skill_insert('MongoDB', 'alice@example.com');
CALL sp_skill_insert('UML', 'alice@example.com');
CALL sp_skill_insert('ERD', 'alice@example.com');
CALL sp_skill_insert('Figma', 'alice@example.com');
CALL sp_skill_insert('Markdown', 'alice@example.com');

-- ==================================================
-- SKILL_CURRICULUM INSERTION (ALL)
-- ==================================================
CALL sp_skill_curriculum_insert('alice@example.com', 'PHP', 5);
CALL sp_skill_curriculum_insert('alice@example.com', 'MySQL', 4);
CALL sp_skill_curriculum_insert('bob@example.com', 'JavaScript', 3);
CALL sp_skill_curriculum_insert('charlie@example.com', 'CSS', 4);
CALL sp_skill_curriculum_insert('charlie@example.com', 'Figma', 4);
CALL sp_skill_curriculum_insert('charlie@example.com', 'JavaScript', 5);
CALL sp_skill_curriculum_insert('diana@example.com', 'PHP', 4);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'MySQL', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'ERD', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'MongoDB', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'PHP', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'CSS', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'HTML', 5);
CALL sp_skill_curriculum_insert('edoardo.galli3@studio.unibo.it', 'JavaScript', 5);
CALL sp_skill_curriculum_insert('frank@example.com', 'Python', 4);
CALL sp_skill_curriculum_insert('grace@example.com', 'JavaScript', 5);
CALL sp_skill_curriculum_insert('heidi@example.com', 'HTML', 3);
CALL sp_skill_curriculum_insert('ivan@example.com', 'C++', 4);
CALL sp_skill_curriculum_insert('judy@example.com', 'SQL', 3);
CALL sp_skill_curriculum_insert('judy@example.com', 'UML', 5);
CALL sp_skill_curriculum_insert('judy@example.com', 'ERD', 5);
CALL sp_skill_curriculum_insert('karen@example.com', 'Ruby', 4);
CALL sp_skill_curriculum_insert('leo@example.com', 'React', 3);
CALL sp_skill_curriculum_insert('mike@example.com', 'Figma', 4);
CALL sp_skill_curriculum_insert('mike@example.com', 'CSS', 5);
CALL sp_skill_curriculum_insert('nancy@example.com', 'Node.js', 5);
CALL sp_skill_curriculum_insert('oscar@example.com', 'SQL', 5);

-- ==================================================
-- PROGETTO INSERTION (CREATORE)
-- ==================================================
CALL sp_progetto_insert(
        'ProgettoAlpha',
        'bob@example.com',
        'Mollit eiusmod deserunt sunt amet do eu anim ipsum sit. Fugiat consectetur aute duis eiusmod adipisicing quis laboris in. Consectetur voluptate cupidatat ipsum id elit.',
        10000.00,
        '2025-12-31',
        'software'
     );

CALL sp_progetto_insert(
        'ProgettoBeta',
        'diana@example.com',
        'Sint consequat officia anim eu voluptate Lorem. velit sint labore excepteur laboris magna do esse. Irure ut aliquip sit aliquip esse qui proident culpa fugiat est elit veniam deserunt.',
        5000.00,
        '2025-11-30',
        'hardware'
     );

CALL sp_progetto_insert(
        'ProgettoGamma',
        'grace@example.com',
        'Sunt voluptate aliqua laboris voluptate adipisicing voluptate mollit do ut aute magna ad. Deserunt nostrud cupidatat ullamco in reprehenderit ex ut in.',
        15000.00,
        '2026-01-16',
        'software'
     );

CALL sp_progetto_insert(
        'ProgettoDelta',
        'ivan@example.com',
        'Incididunt reprehenderit velit irure Lorem do ipsum tempor reprehenderit magna ut dolore sint est incididunt duis. Sit officia irure cillum do. Tempor minim minim ut et mollit et eu adipisicing non.',
        8000.00,
        '2025-06-12',
        'hardware'
     );

CALL sp_progetto_insert(
        'ProgettoEpsilon',
        'karen@example.com',
        'Et velit aliqua ipsum nisi. Qui sunt consectetur eiusmod consequat tempor id aute proident velit velit. Magna cillum et qui mollit sint aliquip aute anim elit. Consectetur veniam in do amet occaecat nostrud aliquip.',
        12000.00,
        '2025-05-31',
        'software'
     );

CALL sp_progetto_insert(
        'ProgettoKappa',
        'ivan@example.com',
        'Magna laborum mollit incididunt officia non elit mollit minim. Cupidatat adipisicing esse sit occaecat. Culpa mollit non aliquip reprehenderit aute eiusmod aliqua nisi aute pariatur mollit.',
        7500.00,
        '2025-03-31',
        'hardware'
     );

-- ==================================================
-- COMPONENTE OPERATIONS (CREATORE)
-- ==================================================
-- For ProgettoBeta:
CALL sp_componente_insert(
        'Comp1',
        'ProgettoBeta',
        'Viti di alta qualità',
        100,
        0.50,
        'diana@example.com'
     );

CALL sp_componente_insert(
        'Comp2',
        'ProgettoBeta',
        'Circuiti stampati',
        10,
        150.00,
        'diana@example.com'
     );

CALL sp_componente_insert(
        'Comp3',
        'ProgettoBeta',
        'LED RGB',
        50,
        1.00,
        'diana@example.com'
     );

CALL sp_componente_insert(
        'Comp4',
        'ProgettoBeta',
        'Metallo lavorato',
        30,
        200.00,
        'diana@example.com'
     );

CALL sp_componente_insert(
        'Comp5',
        'ProgettoBeta',
        'Sensori di movimento',
        15,
        350.00,
        'diana@example.com'
     );

-- ==================================================
-- PROFILO & SKILL_PROFILO OPERATIONS (CREATORE)
-- ==================================================
-- For ProgettoAlpha:
-- Backend Developer
CALL sp_profilo_insert(
        'Backend Developer',
        'ProgettoAlpha',
        'bob@example.com'
        );

CALL sp_skill_profilo_insert(
        'Backend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'MySQL',
        4
        );

CALL sp_skill_profilo_insert(
        'Backend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'MongoDB',
        4
     );

CALL sp_skill_profilo_insert(
        'Backend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'ERD',
        4
     );

CALL sp_skill_profilo_insert(
        'Backend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'PHP',
        3
     );

-- Frontend Developer
CALL sp_profilo_insert(
        'Frontend Developer',
        'ProgettoAlpha',
        'bob@example.com'
     );

CALL sp_skill_profilo_insert(
        'Frontend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'JavaScript',
        4
     );

CALL sp_skill_profilo_insert(
        'Frontend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'CSS',
        3
     );

CALL sp_skill_profilo_insert(
        'Frontend Developer',
        'ProgettoAlpha',
        'bob@example.com',
        'HTML',
        3
     );

-- Project Manager
CALL sp_profilo_insert(
        'Project Manager',
        'ProgettoAlpha',
        'bob@example.com'
     );

CALL sp_skill_profilo_insert(
        'Project Manager',
        'ProgettoAlpha',
        'bob@example.com',
        'UML',
        5
     );

CALL sp_skill_profilo_insert(
        'Project Manager',
        'ProgettoAlpha',
        'bob@example.com',
        'ERD',
        5
     );

CALL sp_skill_profilo_insert(
        'Project Manager',
        'ProgettoAlpha',
        'bob@example.com',
        'SQL',
        3
     );

-- Designer
CALL sp_profilo_insert(
        'Designer',
        'ProgettoAlpha',
        'bob@example.com'
     );

CALL sp_skill_profilo_insert(
        'Designer',
        'ProgettoAlpha',
        'bob@example.com',
        'CSS',
        4
     );

CALL sp_skill_profilo_insert(
        'Designer',
        'ProgettoAlpha',
        'bob@example.com',
        'Figma',
        4
     );

-- Documentation Specialist
CALL sp_profilo_insert(
		'Documentation Specialist',
		'ProgettoAlpha',
		'bob@example.com'
	 );

CALL sp_skill_profilo_insert(
		'Documentation Specialist',
		'ProgettoAlpha',
		'bob@example.com',
		'UML',
		5
	 );

CALL sp_skill_profilo_insert(
		'Documentation Specialist',
		'ProgettoAlpha',
		'bob@example.com',
		'Markdown',
		3
	 );

CALL sp_skill_profilo_insert(
		'Documentation Specialist',
		'ProgettoAlpha',
		'bob@example.com',
		'HTML',
		3
	 );

CALL sp_skill_profilo_insert(
		'Documentation Specialist',
		'ProgettoAlpha',
		'bob@example.com',
		'ERD',
		4
	 );

-- ==================================================
-- PARTECIPANTE OPERATIONS (ALL)
-- ==================================================
-- For ProgettoAlpha:
-- Project Manager
CALL sp_partecipante_utente_insert(
        'judy@example.com',
        'ProgettoAlpha',
        'Project Manager'
     );

CALL sp_partecipante_creatore_update(
        'bob@example.com',
        'judy@example.com',
        'ProgettoAlpha',
        'Project Manager',
        'accettato'
     );

-- Designer
CALL sp_partecipante_utente_insert(
        'mike@example.com',
        'ProgettoAlpha',
        'Designer'
     );

CALL sp_partecipante_creatore_update(
        'bob@example.com',
        'mike@example.com',
        'ProgettoAlpha',
        'Designer',
        'accettato'
     );

-- Frontend Developer
CALL sp_partecipante_utente_insert(
		'edoardo.galli3@studio.unibo.it',
		'ProgettoAlpha',
		'Frontend Developer'
	 );

-- ==================================================
-- COMMENTO OPERATIONS (ALL)
-- ==================================================

CALL sp_commento_insert(
        'heidi@example.com',
        'ProgettoAlpha',
        'Amo il concetto di Alpha!'
     );

CALL sp_commento_insert(
        'mike@example.com',
        'ProgettoAlpha',
        'Gran lavoro, Bob!'
     );

CALL sp_commento_insert(
        'oscar@example.com',
        'ProgettoAlpha',
        'Alpha è un progetto molto interessante.'
     );

CALL sp_commento_risposta_insert(
        1,
        'bob@example.com',
        'ProgettoAlpha',
        'Grazie per il supporto, Heidi!'
     );

CALL sp_commento_risposta_insert(
        2,
        'bob@example.com',
        'ProgettoAlpha',
        'Grazie, Mike!'
     );

CALL sp_commento_insert(
        'judy@example.com',
        'ProgettoBeta',
        'Beta è rivoluzionario!'
     );

CALL sp_commento_risposta_insert(
        4,
        'diana@example.com',
        'ProgettoBeta',
        'Grazie, Judy!'
     );

CALL sp_commento_insert(
        'leo@example.com',
        'ProgettoBeta',
        'Beta sembra robusto e ben pianificato.'
     );

CALL sp_commento_insert(
        'charlie@example.com',
        'ProgettoBeta',
        'Beta è un progetto molto interessante.'
     );