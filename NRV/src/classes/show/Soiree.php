<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Soiree
{
    private int $id_soiree;
    private string $nom;
    private string $thematique;
    private \DateTime $date;
    private \DateTime $horaire;
    private Lieu $lieu;
    private int $tarif;
    private array $spectacles;

    public function __construct($nom, $thematique, $date, $horaire, $lieu, $tarif)
    {
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->lieu = $lieu;
        $this->tarif = $tarif;
        $this->spectacles = [];
    }

    public function __get(string $name) : mixed
    {
        if(property_exists($this, $name))
        {
            return $this->$name;
        }else{
            throw new InvalidPropertyNameException("Erreur : La propriété $name n'existe pas");
        }
    }

    public function setIdSoiree(int $id_soiree) : void
    {
        $this->id_soiree = $id_soiree;
    }

    public function setSpectacles(array $spectacles) : void
    {
        $this->spectacles = $spectacles;
    }

}