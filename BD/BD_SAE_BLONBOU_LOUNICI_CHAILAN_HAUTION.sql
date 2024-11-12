--Création des tables
CREATE TABLE Lieu (
	nom_lieu VARCHAR(255) PRIMARY KEY,
	adresse VARCHAR(255),
	places_assises INT,
	places_debout INT,
	description TEXT
);

CREATE TABLE Soiree (
	id_soiree INT PRIMARY KEY AUTO_INCREMENT,
	nom_soiree VARCHAR(255),
	thematique VARCHAR(255),
	date_soiree DATE,
	horaire_debut TIME,
	horaire_fin TIME,
	nom_lieu VARCHAR(255),
	soiree_tarif DOUBLE(6,2),
	FOREIGN KEY (nom_lieu) REFERENCES Lieu(nom_lieu)
);

CREATE TABLE Spectacle (
	id_spectacle INT PRIMARY KEY AUTO_INCREMENT,
	titre VARCHAR(255),
	description TEXT,
	video_url VARCHAR(255),
	horaire_debut_previsionnel TIME,
	horaire_fin_previsionnel TIME,
	style VARCHAR(255),
	etat VARCHAR(255),
	id_soiree INT,
	FOREIGN KEY (id_soiree) REFERENCES Soiree(id_soiree)
);

CREATE TABLE Artiste (
	id_artiste INT PRIMARY KEY AUTO_INCREMENT,
	nom_artiste VARCHAR(255),
	bio TEXT
);

CREATE TABLE Participe (
	id_spectacle INT,
	id_artiste INT,
	PRIMARY KEY (id_spectacle, id_artiste),
	FOREIGN KEY (id_spectacle) REFERENCES Spectacle(id_spectacle),
	FOREIGN KEY (id_artiste) REFERENCES Artiste(id_artiste)
);

CREATE TABLE ImageLieu (
	url VARCHAR(255) PRIMARY KEY,
	nom_image VARCHAR(255),
	nom_lieu VARCHAR(255),
	FOREIGN KEY (nom_lieu) REFERENCES Lieu(nom_lieu)
);

CREATE TABLE ImageSpectacle (
	url VARCHAR(255) PRIMARY KEY,
	nom_image VARCHAR(255),
	id_spectacle INT,
	FOREIGN KEY (id_spectacle) REFERENCES Spectacle(id_spectacle)
);

CREATE TABLE USERS (
     id_user INT PRIMARY KEY AUTO_INCREMENT,
     username VARCHAR(50) NOT NULL UNIQUE,
     email VARCHAR(100) NOT NULL UNIQUE,
     password VARCHAR(100) NOT NULL,
     role int(3) NOT NULL
);

CREATE TABLE Spectacle2User(
      id_user INT,
      id_spectacle INT,
      FOREIGN KEY (id_user) REFERENCES USERS(id_user),
      FOREIGN KEY (id_spectacle) REFERENCES SPECTACLE(id_spectacle),
      PRIMARY KEY (id_user, id_spectacle)
);

-- Test des tables avec des INSERT INTO quelconque
INSERT INTO Lieu VALUES
	('NRV FESTIVALE CLASSIQUE', '123 Rue de Paris, 54000 NANCY', 500, 200, 'La classe en un endroit classique'),
	('NRV FESTIVALE JAZZ', '45 Avenue des Champs, 54000 NANCY', 300, 150, 'La classe en un endroit jazz'),
	('NRV FESTIVALE RAP', '3 rue de la Cannebiere, 54000 NANCY', 2, 598, 'La classe en un endroit Rap');

INSERT INTO Soiree (nom_soiree, thematique, date_soiree, horaire_debut, horaire_fin, nom_lieu, soiree_tarif)VALUES
	('Soirée Classique', 'Classique', '2023-12-10', '19:00:00', '23:00:00', 'NRV FESTIVALE CLASSIQUE', 200.36),
	('Soirée Jazz', 'Jazz', '2023-12-15', '20:00:00','01:00:00','NRV FESTIVALE JAZZ', 0.36),
	('Soirée Rap', 'Rap', '2023-12-13', '04:00:00', '02:00:00', 'NRV FESTIVALE RAP', 88.88);

INSERT INTO Spectacle (titre, description, video_url, horaire_debut_previsionnel, horaire_fin_previsionnel, style, etat, id_soiree) VALUES
    -- Soirée Classique
    ('Symphonie No.9', 'Performance de la 9ème symphonie de Beethoven', 'http://example.com/video1', '19:30:00', '20:30:00', 'HardClassique', 'confirmé', 1),
    ('Quatuor Mozart', 'Exécution du célèbre quatuor de Mozart en ré majeur', 'http://example.com/video3', '20:45:00', '21:45:00', 'Classique Légendaire', 'annulé', 1),
    ('Nocturnes de Chopin', 'Chopin comme vous ne l\'avez jamais entendu', 'http://example.com/video4', '22:00:00', '23:00:00', 'Romantique', 'confirmé', 1),
    
    -- Soirée Jazz
    ('Jazz Night', 'Une soirée de jazz inoubliable', 'http://example.com/video2', '20:30:00', '23:30:00', 'Salopette Jazz', 'annulé', 2),
    ('Blue Note Session', 'Jam session avec les plus grands du jazz', 'http://example.com/video5', '21:00:00', '22:00:00', 'Bebop', 'confirmé', 2),
    ('Late Night Swing', 'Jazz swing pour les passionnés', 'http://example.com/video6', '23:00:00', '01:00:00', 'Swing', 'confirmé', 2),
    
    -- Soirée Rap
    ('Rap Contenders', 'Battle de rap entre talents émergents', 'http://example.com/video7', '04:30:00', '14:30:00', 'Battle Rap', 'confirmé', 3),
    ('Flow Factory', 'Concert de rap rythmé et intense', 'http://example.com/video8', '14:45:00', '23:45:00', 'Trap', 'annulé', 3),
    ('Old School Revival', 'Retour aux racines du rap des années 90', 'http://example.com/video9', '00:00:00', '02:00:00', 'Old School', 'confirmé', 3);
    
INSERT INTO Artiste (nom_artiste, bio) VALUES
    ('Anne-Sophie Mutter', 'Violoniste allemande de renommée mondiale, spécialiste de la musique classique et des œuvres de Mozart.'),
    ('Yo-Yo Ma', 'Violoncelle américain d’origine chinoise, célèbre pour son interprétation des suites pour violoncelle de Bach.'),
    ('Lang Lang', 'Pianiste chinois, reconnu pour sa virtuosité et son style expressif dans les œuvres classiques.'),
    
    ('Herbie Hancock', 'Pianiste et compositeur américain, l’un des pionniers du jazz fusion et du jazz funk.'),
    ('Norah Jones', 'Chanteuse et pianiste de jazz américaine, célèbre pour sa voix douce et ses ballades jazz.'),
    ('Duke Ellington Orchestra', 'Ensemble de jazz fondé par Duke Ellington, connu pour son influence sur le jazz swing et big band.'),

    ('Kendrick Lamar', 'Rappeur américain primé, reconnu pour ses paroles percutantes et son style unique.'),
    ('Lauryn Hill', 'Rappeuse et chanteuse américaine, icône du hip-hop et du R&B, célèbre pour son album "The Miseducation of Lauryn Hill".'),
    ('A Tribe Called Quest', 'Groupe de rap américain old school, influent dans le développement du jazz rap et du hip-hop conscient.');

INSERT INTO Participe VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(2, 4),
	(2, 5),
	(2, 6),
	(3, 7),
	(3, 8),
	(3, 9);

    
INSERT INTO ImageLieu VALUES
    ('http://example.com/NRV_FESTIVALE_CLASSIQUE.jpg', 'NRV FESTIVALE CLASSIQUE Image', 'NRV FESTIVALE CLASSIQUE'),
    ('http://example.com/NRV_FESTIVALE_JAZZ.jpg', 'NRV FESTIVALE JAZZ Image', 'NRV FESTIVALE JAZZ'),
    ('http://example.com/NRV_FESTIVALE_RAP.jpg', 'NRV FESTIVALE RAP Image', 'NRV FESTIVALE RAP');

    
INSERT INTO ImageSpectacle VALUES
    -- Soirée Classique
    ('http://example.com/symphonie_image.jpg', 'Symphonie No.9 Image', 1),
    ('http://example.com/quatuor_mozart_image.jpg', 'Quatuor Mozart Image', 1),
    ('http://example.com/nocturnes_chopin_image.jpg', 'Nocturnes de Chopin Image', 1),
    
    -- Soirée Jazz
    ('http://example.com/jazznight_image.jpg', 'Jazz Night Image', 2),
    ('http://example.com/blue_note_session_image.jpg', 'Blue Note Session Image', 2),
    ('http://example.com/late_night_swing_image.jpg', 'Late Night Swing Image', 2),
    
    -- Soirée Rap
    ('http://example.com/rap_contenders_image.jpg', 'Rap Contenders Image', 3),
    ('http://example.com/flow_factory_image.jpg', 'Flow Factory Image', 3),
    ('http://example.com/old_school_revival_image.jpg', 'Old School Revival Image', 3);

INSERT INTO USERS VALUES 
(1, 'random_user', 'john@example.com', 'password123', 1),
(2, 'artiste', 'jane@example.com', 'securepassword', 2),
(3, 'admin_user', 'admin@example.com', 'adminpass', 3);

INSERT INTO Spectacle2User VALUES
(2, 2),
(3, 1), 
(3, 2); 