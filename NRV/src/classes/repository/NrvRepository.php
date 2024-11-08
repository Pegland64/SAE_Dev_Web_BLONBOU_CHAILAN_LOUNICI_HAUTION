<?php

namespace nrv\net\repository;

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
     * methode retourne tout les spectacles
     * @return array tableau de spectacles
     */
    public function getAllSpectacle(): array
    {
        $sql = "SELECT * FROM spectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $spectacles[] = new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
        }
        return $spectacles;
    }

    /**
     * methode retourne un spectacle par son id
     * @param int $idSpectacle id du spectacle
     * @return Spectacle objet spectacle
     */
    public function getSpectacleByIdSpectacle(int $idSpectacle): Spectacle
    {
        $sql = "SELECT * FROM spectacle WHERE id_spectacle = :idspectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idspectacle' => $idSpectacle]);
        $row = $stmt->fetch();
        return new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
    }

    /**
     * methode retourne tous les spectacles d'une soiree
     * @param int $idSoiree id de la soiree
     * @return array tableau de spectacles
     */
    public function getAllSpectacleByIdSoiree(int $idSoiree): array
    {
        $sql = "SELECT * FROM spectacle WHERE id_soiree = :idsoiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idsoiree' => $idSoiree]);
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $spectacles[] = new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
        }
        return $spectacles;
    }

    /**
     * methode retourne tous les spectacles par rapport a une date
     * @param string $date date du spectacle
     * @return array tableau de spectacles
     */
    public function getAllSpectacleByDate(string $date): array
    {
        $sql = "SELECT * FROM spectacle inner join soiree on soiree.id_soiree = spectacle.id_soiree where soiree.date_soiree = :date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['date' => $date]);
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $spectacles[] = new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
        }
        return $spectacles;
    }

    /**
     * methode retourne tous les spectacles par rapport a une thematique
     * @param string $thematique thematique du spectacle
     * @return array tableau de spectacles
     */
    public function getAllSpectacleByThematique(string $thematique): array
    {
        $sql = "SELECT * FROM spectacle inner join soiree on soiree.id_soiree = spectacle.id_soiree where soiree.thematique = :thematique";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['thematique' => $thematique]);
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $spectacles[] = new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
        }
        return $spectacles;
    }

    /**
     * methode retourne tous les spectacles par rapport a un lieu
     * @param string $lieu lieu du spectacle
     * @return array tableau de spectacles
     */
    public function getAllSpectacleByLieu(string $lieu): array
    {
        $sql = "SELECT * FROM spectacle inner join soiree on soiree.id_soiree = spectacle.id_soiree where soiree.nom_lieu = :lieu";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['lieu' => $lieu]);
        $spectacles = [];
        while ($row = $stmt->fetch()) {
            $spectacles[] = new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
        }
        return $spectacles;
    }

    /**
     * methode retourne toute les soirées du festival
     * @return array tableau de soirees
     */
    public function getAllSoiree(): array
    {
        $sql = "SELECT * FROM soiree";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $soirees = [];
        while ($row = $stmt->fetch()) {
            $soirees[] = new Soiree($row['id_soiree'], $row['nom_soiree'], $row['thematique'], $row['date_soiree'], $row['horaire_debut'], $row['nom_lieu'], $row['soiree_tarif']);
        }
        return $soirees;
    }

    /**
     * methode qui crée un nouveau spectacle
     * @param string $titre titre du spectacle
     * @param string $description description du spectacle
     * @param string $videoUrl url de la video du spectacle
     * @param string $horairePrevisionnel horaire previsionnel du spectacle
     * @throws PDOException
     */
    public function createSpectacle(string $titre=null, string $description=null, string $videoUrl=null, string $horairePrevisionnel=null): void
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
    public function createSoiree(string $nomSoiree=null, string $thematique=null, string $dateSoiree=null, string $horaireDebut=null, string $nomLieu=null, float $soireeTarif=null): void
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
    



}