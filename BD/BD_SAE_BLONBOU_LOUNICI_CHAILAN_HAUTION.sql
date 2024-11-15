--Cr�ation des tables
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
                     ('NRV FESTIVALE RAP', '3 rue de la Cannebière, 54000 NANCY', 2, 598, 'La classe en un endroit Rap');

INSERT INTO Soiree (nom_soiree, thematique, date_soiree, horaire_debut, horaire_fin, nom_lieu, soiree_tarif) VALUES
                                                                                                                 ('Soirée Classique', 'Classique', '2023-12-10', '19:00:00', '23:00:00', 'NRV FESTIVALE CLASSIQUE', 200.36),
                                                                                                                 ('Soirée Jazz', 'Jazz', '2023-12-15', '20:00:00', '01:00:00', 'NRV FESTIVALE JAZZ', 0.36),
                                                                                                                 ('Soirée Rap', 'Rap', '2023-12-13', '04:00:00', '02:00:00', 'NRV FESTIVALE RAP', 88.88);

INSERT INTO Spectacle (titre, description, video_url, horaire_debut_previsionnel, horaire_fin_previsionnel, style, etat, id_soiree) VALUES
    ('Symphonie No.9', 'Performance de la 9ème symphonie de Beethoven', 'https://www.youtube.com/embed/YlK641jt8H0?si=VN6Pz9gfbMnrHgJ3', '19:30:00', '20:30:00', 'HardClassique', 'confirmé', 1),
    ('Quatuor Mozart', 'Exécution du célèbre quatuor de Mozart en ré majeur', 'https://www.youtube.com/embed/tP6tQ7OEFlY?si=6Vv_Bffq4sl4MFv0', '20:45:00', '21:45:00', 'Classique Légendaire', 'annulé', 1),
    ('Nocturnes de Chopin', 'Chopin comme vous ne l\'avez jamais entendu', 'https://www.youtube.com/embed/9E6b3swbnWg?si=oCFDmB0G4nuWoasL', '22:00:00', '23:00:00', 'Romantique', 'confirmé', 1),
    ('Jazz Night', 'Une soirée de jazz inoubliable', 'https://www.youtube.com/embed/ZEcqHA7dbwM?si=8evxYGOzskBlm8lg', '20:30:00', '23:30:00', 'Salopette Jazz', 'annulé', 2),
    ('Blue Note Session', 'Jam session avec les plus grands du jazz', 'https://www.youtube.com/embed/68ugkg9RePc?si=Av64c_It3U8cpyUE', '21:00:00', '22:00:00', 'Bebop', 'confirmé', 2),
    ('Late Night Swing', 'Jazz swing pour les passionnés', 'https://www.youtube.com/embed/Eco4z98nIQY?si=jWcKyZjOBsWApOwg', '23:00:00', '01:00:00', 'Swing', 'confirmé', 2),
    ('Rap Contenders', 'Battle de rap entre talents émergents', 'https://www.youtube.com/embed/PJlmw3kneSQ?si=Ag82-FHRMLD1lY2w', '04:30:00', '14:30:00', 'Battle Rap', 'confirmé', 3),
    ('Flow Factory', 'Concert de rap rythmé et intense', 'https://www.youtube.com/embed/IhKdk0Wp2uY?si=_9S-ATwoHefzZmpw', '14:45:00', '23:45:00', 'Trap', 'annulé', 3),
    ('Old School Revival', 'Retour aux racines du rap des années 90', 'https://www.youtube.com/embed/AS4GlgkW5Fc?si=hCPRUTyib6VXZWuo', '00:00:00', '02:00:00', 'Old School', 'confirmé', 3);

INSERT INTO Artiste (nom_artiste, bio) VALUES
    ('Anne-Sophie Mutter', 'Violoniste allemande de renommée mondiale, spécialiste de la musique classique et des œuvres de Mozart.'),
    ('Yo-Yo Ma', 'Violoncelle américain d\'origine chinoise, célèbre pour son interprétation des suites pour violoncelle de Bach.'),
                                                                                                                                        ('Lang Lang', 'Pianiste chinois, reconnu pour sa virtuosité et son style expressif dans les œuvres classiques.'),
                                                                                                                                        ('Herbie Hancock', 'Pianiste et compositeur américain, l\'un des pionniers du jazz fusion et du jazz funk.'),
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
    ('https://cso.org/media/maldpiq1/beethoven-9.jpg?mode=max&quality=80&width=1024&upscale=false', 'Symphonie No.9 Image', 1),
    ('https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Wolfgang-amadeus-mozart_1.jpg/1200px-Wolfgang-amadeus-mozart_1.jpg', 'Quatuor Mozart Image', 2),
    ('https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Frederic_Chopin_photo.jpeg/220px-Frederic_Chopin_photo.jpeg', 'Nocturnes de Chopin Image', 3),
    ('https://cdn0.toutcomment.com/fr/posts/1/8/2/quelle_est_l_origine_du_jazz_8281_orig.jpg', 'Jazz Night Image', 4),
    ('https://www.googobits.com/wp-content/uploads/2018/06/blues-music.jpg', 'Blue Note Session Image', 5),
    ('https://highlandartsvt.org/wp-content/uploads/Swingdancing.png', 'Late Night Swing Image', 6),
    ('https://img.nrj.fr/erdknfXiRjC5y-0Fq515pUCrTgk=/800x450/smart/medias%2F2018%2F03%2Fbig-ali.jpg', 'Rap Contenders Image', 7),
    ('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3jBLcfxqwT-IhkjVTLHwtCDfcKXRqH2LZGA&s', 'Flow Factory Image', 8),
    ('https://m.media-amazon.com/images/I/91r7NelJXPL._UF1000,1000_QL80_.jpg', 'Old School Revival Image', 9);

INSERT INTO USERS VALUES
    (1, 'random_user', 'john@example.com', 'password123', 1),
    (2, 'artiste', 'jane@example.com', 'securepassword', 2),
    (3, 'admin_user', 'admin@example.com', 'adminpass', 3);

INSERT INTO Spectacle2User VALUES
    (2, 2),
    (3, 1),
    (3, 2);