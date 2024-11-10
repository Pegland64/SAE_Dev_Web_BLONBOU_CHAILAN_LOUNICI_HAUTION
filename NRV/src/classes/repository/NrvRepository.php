<?php

namespace nrv\net\repository;

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
        $dsn =  'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
        $this->pdo = new \PDO($dsn, $config['username'], $config['password'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function setConfig(string $file) : void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \PDOException("Erreur de lecture du fichier de configuration");
        }

        self::$config = $conf;
    }

    public static function getInstance() : NrvRepository
    {
        if (self::$instance === null) {
            self::$instance = new NrvRepository(self::$config);
        }
        return self::$instance;
    }


    public function getAllSpectacles() : array
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

            $images = $this->getImagesBySpectracleId($id);
            $artistes = $this->getArtisteBySpectracleId($id);

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

    public function getArtisteBySpectracleId(int $id) : array
    {
        $sql = "SELECT * FROM artiste INNER JOIN participe on artiste.id_artiste = participe.id_artiste WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $artistes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $artistes[] = $row['nom_artiste'];
        }
        return $artistes;
    }

    public function getImagesBySpectracleId(int $id) : array
    {
        $sql = "SELECT * FROM imagespectacle WHERE id_spectacle = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $images = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $images[] = $row['url'];
        }
        return $images;
    }

    public function getSpectacleById($id) : Spectacle
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

        $images = $this->getImagesBySpectracleId($id);
        $artistes = $this->getArtisteBySpectracleId($id);

        $spectacle = new Spectacle($titre, $description, $video, $horaire_debut, $duree, $style);
        $spectacle->setIdSpectacle($id);
        $spectacle->setArtistes($artistes);
        $spectacle->setImages($images);
        $spectacle->setEtat($etat);
        $spectacle->setIdSoiree($id_soiree);
        return $spectacle;
    }

    public function getSoireeById($id) : Soiree
    {
        $sql = "SELECT * FROM soiree WHERE id_soiree = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $nom = $row['nom_soiree'];
        $thematique = $row['thematique'];
        $date = new \DateTime($row['date_soiree']);
        $horaire = new \DateTime($row['horaire_debut']);
        $lieu = new Lieu($row['nom_lieu']);
        $tarif = $row['soiree_tarif'];

        $spectacles = $this->getAllSpectacles(); // On récupère tous les spectacles
        $spectaclesSoiree = [];

        foreach ($spectacles as $spectacle) {
            if ($spectacle->id_soiree === $id) {
                $spectaclesSoiree[] = $spectacle;
            }
        }

        $soiree = new Soiree($nom, $thematique, $date, $horaire, $lieu, $tarif);
        $soiree->setIdSoiree($id);
        $soiree->setSpectacles($spectaclesSoiree);

        return $soiree;
    }

    public function getAllSoirees() : array
    {
        $sql = "SELECT * FROM soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id_soiree'];
            $nom = $row['nom_soiree'];
            $thematique = $row['thematique'];
            $date = new \DateTime($row['date_soiree']);
            $horaire = new \DateTime($row['horaire_debut']);
            $lieu = new Lieu($row['nom_lieu']);
            $tarif = $row['soiree_tarif'];

            $soiree = new Soiree($nom, $thematique, $date, $horaire, $lieu, $tarif);
            $soiree->setIdSoiree($id);
            $soirees[] = $soiree;
        }
        return $soirees;
    }


}