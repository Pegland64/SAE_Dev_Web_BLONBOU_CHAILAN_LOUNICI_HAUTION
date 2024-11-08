<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Spectacle
{
    private int $id;
    private string $titre;
    private string $description;
    private string $videoUrl;
    private string $horairePrevisionnel;
    private int $idSoiree;

    public function __construct($id, $titre, $description, $videoUrl, $horairePrevisionnel, $idSoiree) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->videoUrl = $videoUrl;
        $this->horairePrevisionnel = $horairePrevisionnel;
        $this->idSoiree = $idSoiree;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new InvalidPropertyNameException($property);
        }
    }
}