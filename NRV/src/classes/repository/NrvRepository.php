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

    private function __construct(array $conf)
    {
        $dsn = 'mysql:host=' . $conf['host'] . ';dbname=' . $conf['dbname'];
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

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

    public static function getInstance(): ?NrvRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }

    /**
     * methode qui retourne un utilisateur par son id
     * @param int $id id de l'utilisateur
     * @return User objet utilisateur
     */
    public function getUserByUsername(string $username) : User
    {
        // Utilisation de `USERS` au lieu de `user` si la table est en majuscules
        $sql = "SELECT * FROM USERS WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username'=>$username]);
        $row = $stmt->fetch();
        if ($row === false) {
            throw new PDOException("Utilisateur non trouvé.");
        }

        return new User($row['id_user'], $row['username'], $row['password'], $row['email'], (int)$row['role']);
    }

    /**
     * methode retourne tout les spectacles
     * @return array tableau de spectacles
     */
    public function getAllSpectacles(): array
    {
        $sql = "SELECT * FROM spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $style = $row['style'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }
            $interval = $horaire_debut->diff($horaire_fin);
            $duree = $interval->format('%H:%I:%S');
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            $images = $this->getImagesByIdSpectacle($id);
            $artistes = $this->getArtistesBySpectacleId($id);

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
     * methode retourne tout les artistes d'un spectacle par son id
     * @param int $id id du spectacle
     * @return array tableau d'artistes
     */
    public function getArtistesBySpectacleId(int $id): array
    {
        $sql = "SELECT * FROM artiste INNER JOIN participe on artiste.id_artiste = participe.id_artiste WHERE id_spectacle = :id";
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
     * methode retourne un spectacle par son id
     * @param int $idSpectacle id du spectacle
     * @return Spectacle objet spectacle
     */
    public function getSpectacleById(int $idSpectacle): Spectacle
    {
        $sql = "SELECT * FROM spectacle WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idSpectacle]);
        $row = $stmt->fetch();

        if($row === false) {
            throw new PDOException("Spectacle not found.");
        }

        $titre = $row['titre'];
        $description = $row['description'];
        $style = $row['style'];
        $video = $row['video_url'];
        $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
        $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);
        if($horaire_debut > $horaire_fin) {
            $horaire_fin->add(new \DateInterval('P1D'));
        }
        $interval = $horaire_debut->diff($horaire_fin);
        $duree = $interval->format('%H:%I:%S');
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
        $sql = "SELECT * FROM imagespectacle WHERE id_spectacle = :idspectacle";
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


//    /**
//     * methode qui retourne les donnée necessaire pour affichage de spectacle avec l'id du spectacle
//     * à savoir : titre, date, horaire, image, artiste, description, style, durée, extrait
//     * @param int $idSpectacle id du spectacle
//     * @return array tableau de données
//     */
//    public function getDataForRenderSpectacle(int $idSpectacle): array
//    {
//        $sql = "SELECT spectacle.titre, soiree.date_soiree, spectacle.video_url, spectacle.description, artiste.nom_artiste, soiree.thematique, spectacle.horaire_previsionnel
//                FROM spectacle
//                inner join soiree on
//                spectacle.id_soiree = soiree.id_soiree
//                inner join participe ON
//                participe.id_artiste=participe.id_artiste
//                inner JOIN artiste ON
//                artiste.id_artiste = participe.id_artiste
//                WHERE spectacle.id_spectacle = :idspectacle";
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute(['idspectacle' => $idSpectacle]);
//        $row = $stmt->fetch();
//        $img = $this->getImagesByIdSpectacle($idSpectacle);
//        return [
//            'titre' => $row['titre'],
//            'date' => $row['date_soiree'],
//            'horaire' => $row['horaire_previsionnel'],
//            'images' => $img,
//            'artiste' => $row['nom_artiste'],
//            'description' => $row['description'],
//            'style' => $row['thematique'],
//            'duree' => '1h30',
//            'extrait' => $row['video_url']
//        ];
//    }


    /**
     * methode qui retourne une soiree par son id
     * @param int $id id de la soiree
     * @return Soiree objet soiree
     */
    public function getSoireeById(int $id): Soiree
    {
        $sql = "SELECT * FROM soiree WHERE id_soiree = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if($row === false) {
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
        $interval = $horaire_debut->diff($horaire_fin);
        $duree = $interval->format('%H:%I:%S');
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
     * methode qui retourne toutes les soirees
     * @return array tableau de soirees
     */
    public function getAllSoirees(): array
    {
        $sql = "SELECT * FROM soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $id = $row['id_soiree'];
            $nom = $row['nom_soiree'];
            $thematique = $row['thematique'];
            $date = new \DateTime($row['date_soiree']);
            $horaire_debut = new \DateTime($row['horaire_debut']);
            $horaire_fin = new \DateTime($row['horaire_fin']);
            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }
            $interval = $horaire_debut->diff($horaire_fin);
            $duree = $interval->format('%H:%I:%S');
            $tarif = $row['soiree_tarif'];

            $lieu = $this->getLieuByNom($row['nom_lieu']);

            $soiree = new Soiree($nom, $thematique, $date, $horaire_debut, $lieu, $tarif);
            $soiree->setIdSoiree($id);
            $soiree->setDuree($duree);
            $soirees[] = $soiree;
        }
        return $soirees;
    }


    /**
     * methode qui retourne un lieu par son nom
     * @param $nom nom du lieu
     * @return Lieu objet lieu
     */
    public function getLieuByNom($nom): Lieu
    {
        $sql = "SELECT * FROM lieu WHERE nom_lieu = :nom";
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
     * methode qui crée un nouveau spectacle
     * @param string $titre titre du spectacle
     * @param string $description description du spectacle
     * @param string $videoUrl url de la video du spectacle
     * @param string $horairePrevisionnel horaire previsionnel du spectacle
     * @throws PDOException
     */
    public function createSpectacle(string $titre = null, string $description = null, string $videoUrl = null, string $horairePrevisionnel = null): void
    {
        $sql = "INSERT INTO spectacle (titre, description, video_url, horaire_previsionnel, etat) VALUES (:titre, :description, :video_url, :horaire_previsionnel, :etat)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['titre' => $titre, 'description' => $description, 'video_url' => $videoUrl, 'horaire_previsionnel' => $horairePrevisionnel, 'etat' => 'confirmé']);
    }

    /**
     * methode qui crée une nouvelle soiree
     * @param string $nomSoiree nom de la soiree
     * @param string $thematique thematique de la soiree
     * @param string $dateSoiree date de la soiree
     * @param string $horaireDebut horaire de debut de la soiree
     * @param string $nomLieu nom du lieu de la soiree
     * @param float $soireeTarif tarif de la soiree
     */
    public function createSoiree(string $nomSoiree = null, string $thematique = null, string $dateSoiree = null, string $horaireDebut = null, string $nomLieu = null, float $soireeTarif = null): void
    {
        $sql = "INSERT INTO soiree (nom_soiree, thematique, date_soiree, horaire_debut, nom_lieu, soiree_tarif) VALUES (:nom_soiree, :thematique, :date_soiree, :horaire_debut, :nom_lieu, :soiree_tarif)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom_soiree' => $nomSoiree, 'thematique' => $thematique, 'date_soiree' => $dateSoiree, 'horaire_debut' => $horaireDebut, 'nom_lieu' => $nomLieu, 'soiree_tarif' => $soireeTarif]);
    }

    /**
     * methode qui ajoute un spectacle a une soiree
     * @param int $idSpectacle id du spectacle
     * @param int $idSoiree id de la soiree
     */
    public function addSpectacleToSoiree(int $idSpectacle, int $idSoiree): void
    {
        $sql = "UPDATE spectacle SET id_soiree = :id_soiree WHERE id_spectacle = :id_spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_soiree' => $idSoiree, 'id_spectacle' => $idSpectacle]);
    }

    /**
     * methode qui annule un spectacle
     * @param int $idSpectacle id du spectacle
     */
    public function cancelSpectacle(int $idSpectacle): void
    {
        $sql = "UPDATE spectacle SET etat = :etat WHERE id_spectacle = :id_spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['etat' => 'annulé', 'id_spectacle' => $idSpectacle]);
    }


//    /**
//     * methode qui retourne les données pour l'affichage d'un soirée
//     * @param int $idSoiree id de la soiree
//     * @return array tableau de données
//     */
//    public function getDataForRenderSoiree(int $idSoiree): array
//    {
//        $sql = "SELECT soiree.nom_soiree, soiree.thematique, soiree.date_soiree, soiree.horaire_debut, soiree.nom_lieu, soiree.soiree_tarif
//                FROM soiree
//                WHERE soiree.id_soiree = :idsoiree";
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute(['idsoiree' => $idSoiree]);
//        $row = $stmt->fetch();
//        $spectacles = $this->getAllSpectacleByIdSoiree($idSoiree);
//        return [
//            'nom_soiree' => $row['nom_soiree'],
//            'thematique' => $row['thematique'],
//            'date_soiree' => $row['date_soiree'],
//            'horaire_debut' => $row['horaire_debut'],
//            'nom_lieu' => $row['nom_lieu'],
//            'soiree_tarif' => $row['soiree_tarif'],
//            'spectacles' => $spectacles
//        ];
//    }

    /**
     * methode qui retourne un la liste des categories pour le tri
     * @param $category le type de categorie
     * @return array la liste des categories
     */
    public function getOptionsByCategory($category): array
    {
        switch ($category) {
            case 'date':
                $sql = "SELECT DISTINCT date_soiree FROM soiree";
                break;
            case 'lieu':
                $sql = "SELECT DISTINCT nom_lieu FROM soiree";
                break;
            case 'style':
                $sql = "SELECT DISTINCT style FROM spectacle";
                break;
            default:
                return [];
        }

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * methode qui retourne les spectacles filtrés par categorie
     * @param $category la categorie
     * @param $filter le filtre
     * @return array la liste des spectacles filtrés
     */
    public function getFilteredSpectaclesByCategory($category, $filter): array
    {
        $sql = "SELECT s.* FROM spectacle s
                JOIN soiree so ON s.id_soiree = so.id_soiree
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