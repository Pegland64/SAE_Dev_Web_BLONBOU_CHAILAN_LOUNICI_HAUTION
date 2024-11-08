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

    public function getSpectacleByIdSpectacle(int $idSpectacle): Spectacle
    {
        $sql = "SELECT * FROM spectacle WHERE id_spectacle = :idspectacle";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idspectacle' => $idSpectacle]);
        $row = $stmt->fetch();
        return new Spectacle($row['id_spectacle'], $row['titre'], $row['description'], $row['video_url'], $row['horaire_previsionnel'], $row['id_soiree']);
    }

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

}