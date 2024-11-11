<?php

namespace nrv\net\repository;

use nrv\net\show\Artiste;
use nrv\net\show\Image;
use nrv\net\show\Lieu;
use nrv\net\show\Soiree;
use nrv\net\show\Spectacle;

class NrvRepository
{
    private \PDO $pdo;
    private static ?NrvRepository $instance = null;
    private static array $config = [];

    private function __construct(array $config)
    {
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
        $this->pdo = new \PDO($dsn, $config['username'], $config['password'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \PDOException("Erreur de lecture du fichier de configuration");
        }

        self::$config = $conf;
    }

    public static function getInstance(): NrvRepository
    {
        if (self::$instance === null) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }


    public function getAllSpectacles(): array
    {
        $sql = "SELECT * FROM spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id_spectacle'];
            $titre = $row['titre'];
            $description = $row['description'];
            $style = $row['style'];
            $video = $row['video_url'];
            $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
            $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);
            $interval = $horaire_debut->diff($horaire_fin);
            $duree = $interval->format('%H:%I:%S');
            $etat = $row['etat'];
            $id_soiree = $row['id_soiree'];

            $images = $this->getImagesBySpectacleId($id);
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

    public function getArtistesBySpectacleId(int $id): array
    {
        $sql = "SELECT * FROM artiste INNER JOIN participe on artiste.id_artiste = participe.id_artiste WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $artistes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $artiste = new Artiste($row['nom_artiste'], $row['bio']);
            $artiste->setIdArtiste($row['id_artiste']);
            $artistes[] = $artiste;
        }
        return $artistes;
    }

    public function getImagesBySpectacleId(int $id): array
    {
        $sql = "SELECT * FROM imagespectacle WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $images = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $image = new Image($row['url'], $row['nom_image']);
            $image->setIdSpectacle($row['id_spectacle']);
            $images[] = $image;
        }
        return $images;
    }

    public function getSpectacleById($id): Spectacle
    {
        $sql = "SELECT * FROM spectacle WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $titre = $row['titre'];
        $description = $row['description'];
        $style = $row['style'];
        $video = $row['video_url'];
        $horaire_debut = new \DateTime($row['horaire_debut_previsionnel']);
        $horaire_fin = new \DateTime($row['horaire_fin_previsionnel']);
        $interval = $horaire_debut->diff($horaire_fin);
        $duree = $interval->format('%H:%I:%S');
        $etat = $row['etat'];
        $id_soiree = $row['id_soiree'];

        $images = $this->getImagesBySpectacleId($id);
        $artistes = $this->getArtistesBySpectacleId($id);

        $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
        $spectacle->setIdSpectacle($id);
        $spectacle->setArtistes($artistes);
        $spectacle->setImages($images);
        $spectacle->setEtat($etat);
        $spectacle->setIdSoiree($id_soiree);
        return $spectacle;
    }

    public function getSoireeById($id): Soiree
    {
        $sql = "SELECT * FROM soiree WHERE id_soiree = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $nom = $row['nom_soiree'];
        $thematique = $row['thematique'];
        $date = new \DateTime($row['date_soiree']);
        $horaire_debut = new \DateTime($row['horaire_debut']);
        $horaire_fin = new \DateTime($row['horaire_fin']);
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

    public function getAllSoirees(): array
    {
        $sql = "SELECT * FROM soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id_soiree'];
            $nom = $row['nom_soiree'];
            $thematique = $row['thematique'];
            $date = new \DateTime($row['date_soiree']);
            $horaire_debut = new \DateTime($row['horaire_debut']);
            $horaire_fin = new \DateTime($row['horaire_fin']);
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

    public function getLieuByNom($nom): Lieu
    {
        $sql = "SELECT * FROM lieu WHERE nom_lieu = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $lieu = new Lieu($row['nom_lieu'], $row['adresse'], $row['places_assises'], $row['places_debout'], $row['description']);
        return $lieu;
    }

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
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $spectacles = [];
        foreach ($rows as $row) {
            $spectacles[] = $this->getSpectacleById($row['id_spectacle']);
        }

        return $spectacles;
    }

}