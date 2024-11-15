<?php

namespace nrv\net\repository;

use nrv\net\show\Artiste;
use nrv\net\show\Image;
use nrv\net\show\Lieu;
use nrv\net\show\Spectacle;
use nrv\net\show\Soiree;
use nrv\net\user\User;
use PDO;
use PDOException;


class NrvRepository
{
    private PDO $pdo;
    private static ?NrvRepository $instance = null;
    private static array $config = [];

    /**
     * Constructeur de la classe NrvRepository.
     *
     * @param array $conf Configuration pour la connexion à la base de données.
     */
    private function __construct(array $conf)
    {
        $dsn = 'mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'];
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * Définit la configuration de la base de données à partir d'un fichier.
     *
     * @param string $file Chemin vers le fichier de configuration.
     * @throws PDOException Si la lecture du fichier de configuration échoue.
     */
    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new PDOException("Erreur lors de la lecture du fichier de configuration.");
        }

        self::$config = [
            'host' => $conf['host'] ?? null,
            'dbname' => $conf['dbname'] ?? null,
            'user' => $conf['username'] ?? null,
            'pass' => $conf['password'] ?? null
        ];
    }

    /**
     * Retourne l'instance unique de NrvRepository.
     *
     * @return NrvRepository|null L'instance de NrvRepository ou null si non initialisée.
     */
    public static function getInstance(): ?NrvRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }

    /**
     * Ajoute un nouvel utilisateur à la base de données.
     *
     * @param string $username Le nom d'utilisateur.
     * @param string $email L'adresse e-mail de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return void
     */
    public function addUser(string $username, string $email, string $password): void
    {
        $sql = "INSERT INTO USERS (username, email, password, role) VALUES (:username, :email, :password, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'password' => $password, 'email' => $email]);
    }

    /**
     * Met à jour le rôle d'un utilisateur.
     *
     * @param User $user L'utilisateur dont le rôle doit être mis à jour.
     * @param string $role Le nouveau rôle de l'utilisateur.
     * @return void
     */
    public function updateRoleUser(User $user, string $role): void
    {
        $sql = "UPDATE USERS SET role = :role WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role' => $role, 'id' => $user->id]);
    }


    /**
     * Supprime un utilisateur de la base de données.
     *
     * @param User $user L'utilisateur à supprimer.
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $sql = "DELETE FROM USERS WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $user->id]);
    }

    /**
     * methode qui retourne un utilisateur par son id
     * @param int $id id de l'utilisateur
     * @return User objet utilisateur
     */
    public function getUserByUsername(string $username): User
    {
        $sql = "SELECT * FROM USERS WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        if ($row === false) {
            throw new PDOException("Utilisateur non trouvé.");
        }

        return new User($row['id_user'], $row['username'], $row['password'], $row['email'], (int)$row['role']);
    }

    /**
     * Récupère un utilisateur par son identifiant.
     *
     * @param int $id L'identifiant de l'utilisateur.
     * @return User L'objet utilisateur.
     * @throws PDOException Si l'utilisateur n'est pas trouvé.
     */
    public function getUserbyId(int $id): User
    {
        $sql = "SELECT * FROM USERS WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row === false) {
            throw new PDOException("Utilisateur non trouvé.");
        }

        return new User($row['id_user'], $row['username'], $row['password'], $row['email'], (int)$row['role']);
    }

    /**
     * Récupère tous les utilisateurs.
     *
     * @return array La liste des utilisateurs.
     */
    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM USERS";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = [];
        while ($row = $stmt->fetch()) {
            $user = new User($row['id_user'], $row['username'], $row['password'], $row['email'], (int)$row['role']);
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Méthode qui retourne tous les spectacles.
     *
     * @return array Tableau de spectacles.
     */
    public function getAllSpectacles(): array
    {
        $sql = "SELECT * FROM Spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            // Récupération des données du spectacle
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $style = $row['style'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);

            // Ajustement de l'horaire de fin si nécessaire
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            // Calcul de la durée du spectacle
            $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            // Récupération des images et des artistes associés au spectacle
            $images = $this->getImagesByIdSpectacle($id);
            $artistes = $this->getArtistesBySpectacleId($id);

            // Création de l'objet Spectacle
            $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
            $spectacle->setIdSpectacle($id);
            $spectacle->setArtistes($artistes);
            $spectacle->setImages($images);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);

            // Ajout du spectacle au tableau des spectacles
            $spectacles[] = $spectacle;
        }
        return $spectacles;
    }

    /**
     * methode retourne tout les artistes d'un spectacle par son id
     * @param int $id id du spectacle
     * @return array tableau d'artistes
     */
    public function getArtistesBySpectacleId(int $id): array
    {
        $sql = "SELECT * FROM Artiste INNER JOIN Participe ON Artiste.id_artiste = Participe.id_artiste WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $artistes = [];
        while ($row = $stmt->fetch()) {
            $artiste = new Artiste($row['nom_artiste'], $row['bio']);
            $artiste->setIdArtiste($row['id_artiste']);
            $artistes[] = $artiste;
        }
        return $artistes;
    }

    /**
     * Méthode qui retourne un spectacle par son identifiant.
     *
     * @param int $idSpectacle Identifiant du spectacle.
     * @return Spectacle Objet spectacle.
     */
    public function getSpectacleById(int $idSpectacle): Spectacle
    {
        $sql = "SELECT * FROM Spectacle WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idSpectacle]);
        $row = $stmt->fetch();

        if ($row === false) {
            throw new PDOException("Spectacle non trouvé.");
        }

        $titre = $row['titre'];
        $description = $row['description'];
        $style = $row['style'];
        $video = $row['video_url'];
        $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
        $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);
        if ($horaire_debut > $horaire_fin) {
            $horaire_fin->add(new \DateInterval('P1D'));
        }
        $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
        $etat = $row['etat'];
        $id_soiree = $row['id_soiree'];

        $images = $this->getImagesByIdSpectacle($idSpectacle);
        $artistes = $this->getArtistesBySpectacleId($idSpectacle);

        $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
        $spectacle->setIdSpectacle($idSpectacle);
        $spectacle->setArtistes($artistes);
        $spectacle->setImages($images);
        $spectacle->setEtat($etat);
        $spectacle->setIdSoiree($id_soiree);
        return $spectacle;
    }

    /**
     * methode qui retourne un tableau d'image pour un spectacle
     * @param int $idSpectacle id du spectacle
     * @return array tableau d'images
     */
    public function getImagesByIdSpectacle(int $idSpectacle): array
    {
        $sql = "SELECT * FROM ImageSpectacle WHERE id_spectacle = :idspectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idspectacle' => $idSpectacle]);

        $images = [];
        while ($row = $stmt->fetch()) {
            $image = new Image($row['url'], $row['nom_image']);
            $image->setIdSpectacle($row['id_spectacle']);
            $images[] = $image;
        }

        return $images;
    }

    /**
     * methode qui retourne une soiree par son id
     * @param int $id id de la soiree
     * @return Soiree objet soiree
     */
    public function getSoireeById(int $id): Soiree
    {
        $sql = "SELECT * FROM Soiree WHERE id_soiree = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            throw new PDOException("Soiree not found.");
        }

        $nom = $row['nom_soiree'];
        $thematique = $row['thematique'];
        $date = new \DateTime($row['date_soiree']);
        $horaire_debut = new \DateTime($row['horaire_debut']);
        $horaire_fin = new \DateTime($row['horaire_fin']);
        if ($horaire_debut > $horaire_fin) {
            $horaire_fin->add(new \DateInterval('P1D'));
        }
        $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
        $tarif = $row['soiree_tarif'];
        $lieu = $this->getLieuByNom($row['nom_lieu']);

        $spectacles = $this->getAllSpectacles(); // On récupère tous les spectacles
        $spectaclesSoiree = [];

        foreach ($spectacles as $spectacle) {
            if ($spectacle->id_soiree === $id) {
                $spectaclesSoiree[] = $spectacle;
            }
        }

        $soiree = new Soiree($nom, $thematique, $date, $horaire_debut, $lieu, $tarif);
        $soiree->setIdSoiree($id);
        $soiree->setSpectacles($spectaclesSoiree);
        $soiree->setDuree($duree);

        return $soiree;
    }

    /**
     * Ajoute une nouvelle soirée à la base de données.
     *
     * @param Soiree $soiree La soirée à ajouter.
     * @throws PDOException Si le lieu de la soirée n'est pas trouvé.
     */
    public function addSoiree(Soiree $soiree): void
    {
        // Vérifie si le lieu existe dans la base de données
        $sql = "SELECT COUNT(*) FROM Lieu WHERE nom_lieu = :nom_lieu";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom_lieu' => $soiree->lieu->nom]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Lieu not found.");
        }

        // Insère une nouvelle soirée dans la base de données
        $sql = "INSERT INTO Soiree (nom_soiree, thematique, date_soiree, horaire_debut, horaire_fin, nom_lieu, soiree_tarif) VALUES (:nom_soiree, :thematique, :date_soiree, :horaire_debut, :horaire_fin, :nom_lieu, :soiree_tarif)";
        $stmt = $this->pdo->prepare($sql);

        // Calcule la durée de la soirée
        $durationParts = explode(':', $soiree->duree->format('H:i:s'));
        $intervalSpec = sprintf('PT%sH%sM%sS', $durationParts[0], $durationParts[1], $durationParts[2]);

        // Exécute la requête d'insertion avec les paramètres de la soirée
        $stmt->execute([
            'nom_soiree' => $soiree->nom,
            'thematique' => $soiree->thematique,
            'date_soiree' => $soiree->date->format('Y-m-d'),
            'horaire_debut' => $soiree->horaire->format('H:i:s'),
            'horaire_fin' => (clone $soiree->horaire)->add(new \DateInterval($intervalSpec))->format('H:i:s'),
            'nom_lieu' => $soiree->lieu->nom,
            'soiree_tarif' => $soiree->tarif
        ]);
    }

    /**
     * Méthode qui retourne toutes les soirées.
     *
     * @return array Tableau de soirées.
     */
    public function getAllSoirees(): array
    {
        $sql = "SELECT * FROM Soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $soirees = [];
        while ($row = $stmt->fetch()) {
            $id = $row['id_soiree'];
            $nom = $row['nom_soiree'];
            $thematique = $row['thematique'];
            $date = new \DateTime($row['date_soiree']);
            $horaire_debut = new \DateTime($row['horaire_debut']);
            $horaire_fin = new \DateTime($row['horaire_fin']);

            // Ajustement de l'horaire de fin si nécessaire
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            // Calcul de la durée de la soirée
            $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
            $tarif = $row['soiree_tarif'];

            // Récupération du lieu de la soirée
            $lieu = $this->getLieuByNom($row['nom_lieu']);

            // Création de l'objet Soiree
            $soiree = new Soiree($nom, $thematique, $date, $horaire_debut, $lieu, $tarif);
            $soiree->setIdSoiree($id);
            $soiree->setDuree($duree);
            $soirees[] = $soiree;
        }
        return $soirees;
    }

    /**
     * Récupère tous les artistes.
     *
     * @return array La liste des artistes.
     */
    public function getAllArtistes(): array
    {
        $sql = "SELECT * FROM Artiste";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $artistes = [];
        while ($row = $stmt->fetch()) {
            // Création de l'objet Artiste
            $artiste = new Artiste($row['nom_artiste'], $row['bio']);
            $artiste->setIdArtiste($row['id_artiste']);
            $artistes[] = $artiste;
        }
        return $artistes;
    }

    /**
     * Méthode qui renvoie tous les spectacles par rapport à un lieu, à l'exception d'un spectacle passé en paramètre.
     *
     * @param string $lieu Nom du lieu.
     * @param int $idspec Identifiant du spectacle à exclure.
     * @return array Tableau de spectacles.
     */
    public function getSpectaclesByLieu(string $lieu, int $idspec): array
    {
        $sql = "SELECT * FROM Spectacle 
            INNER JOIN Soiree ON Soiree.id_soiree = Spectacle.id_soiree 
            INNER JOIN Lieu ON Lieu.nom_lieu = Soiree.nom_lieu 
            WHERE Lieu.nom_lieu = :nomlieu AND Spectacle.id_spectacle != :idspec";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nomlieu' => $lieu, 'idspec' => $idspec]);
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $style = $row['style'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);

            // Ajustement de l'horaire de fin si nécessaire
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            // Calcul de la durée du spectacle
            $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            // Récupération des images et des artistes associés au spectacle
            $images = $this->getImagesByIdSpectacle($id);
            $artistes = $this->getArtistesBySpectacleId($id);

            // Création de l'objet Spectacle
            $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
            $spectacle->setIdSpectacle($id);
            $spectacle->setArtistes($artistes);
            $spectacle->setImages($images);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);
            $spectacles[] = $spectacle;
        }
        return $spectacles;
    }

    /**
     * Méthode qui renvoie tous les spectacles par rapport à un style, à l'exception d'un spectacle passé en paramètre.
     *
     * @param string $style Le style du spectacle.
     * @param int $idspec L'identifiant du spectacle à exclure.
     * @return array La liste des spectacles.
     */
    public function getSpectaclesByStyle(string $style, int $idspec): array
    {
        // Prépare la requête SQL pour sélectionner les spectacles par style, en excluant un spectacle spécifique
        $sql = "SELECT * FROM Spectacle WHERE style = :style AND id_spectacle != :idspec";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['style' => $style, 'idspec' => $idspec]);
        $spectacles = [];

        // Parcourt les résultats de la requête
        while ($row = $stmt->fetch()) {
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);

            // Ajuste l'horaire de fin si nécessaire
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            // Calcule la durée du spectacle
            $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            // Récupère les images et les artistes associés au spectacle
            $images = $this->getImagesByIdSpectacle($id);
            $artistes = $this->getArtistesBySpectacleId($id);

            // Crée l'objet Spectacle
            $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
            $spectacle->setIdSpectacle($id);
            $spectacle->setArtistes($artistes);
            $spectacle->setImages($images);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);

            // Ajoute le spectacle au tableau des spectacles
            $spectacles[] = $spectacle;
        }

        // Retourne la liste des spectacles
        return $spectacles;
    }

    /**
     * Récupère la date d'un spectacle par son identifiant.
     *
     * @param int $id L'identifiant du spectacle.
     * @return string La date de la soirée associée au spectacle.
     * @throws PDOException Si le spectacle n'est pas trouvé.
     */
    public function getDateSpectacle(int $id): string
    {
        $sql = "SELECT date_soiree FROM Spectacle INNER JOIN Soiree ON Spectacle.id_soiree = Soiree.id_soiree WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row === false) {
            throw new PDOException("Spectacle non trouvé.");
        }
        return $row['date_soiree'];
    }

    /**
     * Méthode qui renvoie tous les spectacles par rapport à une date passée en paramètre.
     *
     * @param string $date Date de la soirée.
     * @param int $idspec Identifiant du spectacle à exclure.
     * @return array Tableau de spectacles.
     */
    public function getSpectaclesByDate(string $date, int $idspec): array
    {
        // Prépare la requête SQL pour sélectionner les spectacles par date, en excluant un spectacle spécifique
        $sql = "SELECT * FROM Spectacle INNER JOIN Soiree ON Soiree.id_soiree = Spectacle.id_soiree WHERE Soiree.date_soiree = :date AND Spectacle.id_spectacle != :idspec";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['date' => $date, 'idspec' => $idspec]);
        $spectacles = [];

        // Parcourt les résultats de la requête
        while ($row = $stmt->fetch()) {
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $style = $row['style'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);

            // Ajuste l'horaire de fin si nécessaire
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            // Calcule la durée du spectacle
            $duree = (new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin));
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            // Récupère les images et les artistes associés au spectacle
            $images = $this->getImagesByIdSpectacle($id);
            $artistes = $this->getArtistesBySpectacleId($id);

            // Crée l'objet Spectacle
            $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
            $spectacle->setIdSpectacle($id);
            $spectacle->setArtistes($artistes);
            $spectacle->setImages($images);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);

            // Ajoute le spectacle au tableau des spectacles
            $spectacles[] = $spectacle;
        }

        // Retourne la liste des spectacles
        return $spectacles;
    }


    /**
     * Méthode qui retourne un lieu par son nom.
     *
     * @param string $nom Nom du lieu.
     * @return Lieu Objet lieu.
     * @throws PDOException Si le lieu n'est pas trouvé.
     */
    public function getLieuByNom(string $nom): Lieu
    {
        $sql = "SELECT * FROM Lieu WHERE nom_lieu = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        $row = $stmt->fetch();
        if ($row === false) {
            throw new PDOException("Lieu not found.");
        }
        $lieu = new Lieu($row['nom_lieu'], $row['adresse'], $row['places_assises'], $row['places_debout'], $row['description']);
        return $lieu;
    }

    /**
     * Méthode qui retourne tous les lieux.
     *
     * @return array La liste des lieux.
     */
    public function getAllLieux(): array
    {
        $sql = "SELECT * FROM Lieu";
        $stmt = $this->pdo->query($sql);
        $stmt->execute();
        $lieux = [];
        while ($row = $stmt->fetch()) {
            $lieu = new Lieu($row['nom_lieu'], $row['adresse'], $row['places_assises'], $row['places_debout'], $row['description']);
            $lieux[] = $lieu;
        }
        return $lieux;
    }

    /**
     * Ajoute un nouveau spectacle à la base de données.
     *
     * @param Spectacle $spectacle Le spectacle à ajouter.
     * @param array $artistes La liste des identifiants des artistes participant au spectacle.
     * @param array $images Les images associées au spectacle.
     * @return void
     * @throws PDOException Si la soirée associée au spectacle n'est pas trouvée.
     */
    public function addSpectacle(Spectacle $spectacle, array $artistes, array $images): void
    {
        // Vérifie si la soirée existe dans la base de données
        $sql = "SELECT COUNT(*) FROM Soiree WHERE id_soiree = :id_soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_soiree' => $spectacle->id_soiree]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Soiree not found.");
        }

        // Insère un nouveau spectacle dans la base de données
        $sql = "INSERT INTO Spectacle (titre, description, video_url, horaire_debut_previsionnel, horaire_fin_previsionnel, style, etat, id_soiree)
        VALUES (:titre, :description, :video_url, :horaire_debut, :horaire_fin, :style, :etat, :id_soiree)";
        $stmt = $this->pdo->prepare($sql);

        // Calcule l'intervalle de durée du spectacle
        $interval = $spectacle->duree->diff(new \DateTime('00:00:00'));
        $stmt->execute([
            'titre' => $spectacle->titre,
            'description' => $spectacle->description,
            'video_url' => $spectacle->video,
            'horaire_debut' => $spectacle->horaire->format('H:i:s'),
            'horaire_fin' => ($spectacle->horaire->add($interval)->format('H:i:s')),
            'style' => $spectacle->style,
            'etat' => $spectacle->etat,
            'id_soiree' => $spectacle->id_soiree
        ]);

        // Récupère l'identifiant du spectacle inséré
        $idSpectacle = $this->pdo->lastInsertId();

        // Insère les artistes associés au spectacle
        $sql = "INSERT INTO Participe (id_spectacle, id_artiste) VALUES (:id_spectacle, :id_artiste)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($artistes as $idArtiste) {
            $stmt->execute(['id_spectacle' => $idSpectacle, 'id_artiste' => $idArtiste]);
        }

        // Insère les images associées au spectacle
        if (!empty($images['name'][0])) {
            $sql = "INSERT INTO ImageSpectacle (url, nom_image, id_spectacle) VALUES (:url, :nom_image, :id_spectacle)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($images['name'] as $key => $name) {
                $tmpName = $images['tmp_name'][$key];
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $randomName = uniqid() . '.' . $extension;
                $filePath = 'images/' . $randomName;
                move_uploaded_file($tmpName, $filePath);

                $stmt->execute(['url' => $filePath, 'nom_image' => $name, 'id_spectacle' => $idSpectacle]);
            }
        }
    }

    /**
     * Met à jour un spectacle dans la base de données.
     *
     * @param Spectacle $spectacle Le spectacle à mettre à jour.
     * @return void
     */
    public function updateSpectacle(Spectacle $spectacle): void
    {
        // Prépare la requête SQL pour mettre à jour le spectacle
        $sql = "UPDATE Spectacle
        SET titre = :titre,
            description = :description,
            video_url = :video_url,
            horaire_debut_previsionnel = :horaire_debut,
            horaire_fin_previsionnel = :horaire_fin,
            style = :style,
            etat = :etat,
            id_soiree = :id_soiree
        WHERE id_spectacle = :id_spectacle";
        $stmt = $this->pdo->prepare($sql);

        // Exécute la requête avec les paramètres du spectacle
        $stmt->execute([
            'titre' => $spectacle->titre,
            'description' => $spectacle->description,
            'video_url' => $spectacle->video,
            'horaire_debut' => $spectacle->horaire->format('H:i:s'),
            'horaire_fin' => $spectacle->duree->format('H:i:s'),
            'style' => $spectacle->style,
            'etat' => $spectacle->etat,
            'id_soiree' => $spectacle->id_soiree,
            'id_spectacle' => $spectacle->id_spectacle
        ]);
    }


    /**
     * Méthode qui retourne la liste des catégories pour le tri.
     *
     * @param string $category Le type de catégorie (date, lieu, style).
     * @return array La liste des catégories.
     */
    public function getOptionsByCategory($category): array
    {
        switch ($category) {
            case 'date':
                $sql = "SELECT DISTINCT date_soiree FROM Soiree";
                break;
            case 'lieu':
                $sql = "SELECT DISTINCT nom_lieu FROM Soiree";
                break;
            case 'style':
                $sql = "SELECT DISTINCT style FROM Spectacle";
                break;
            default:
                return [];
        }

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Méthode qui retourne les spectacles filtrés par catégorie.
     *
     * @param string $category La catégorie (date, lieu, style).
     * @param string $filter Le filtre appliqué à la catégorie.
     * @return array La liste des spectacles filtrés.
     */
    public function getFilteredSpectaclesByCategory($category, $filter): array
    {
        $sql = "SELECT s.* FROM Spectacle s
            JOIN Soiree so ON s.id_soiree = so.id_soiree
            WHERE 1=1";
        $params = [];

        if ($filter && $filter !== 'all') {
            switch ($category) {
                case 'date':
                    $sql .= " AND so.date_soiree = :filter";
                    break;
                case 'lieu':
                    $sql .= " AND so.nom_lieu = :filter";
                    break;
                case 'style':
                    $sql .= " AND s.style = :filter";
                    break;
            }
            $params['filter'] = $filter;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $spectacles = [];
        foreach ($rows as $row) {
            $spectacles[] = $this->getSpectacleById($row['id_spectacle']);
        }

        return $spectacles;
    }
}