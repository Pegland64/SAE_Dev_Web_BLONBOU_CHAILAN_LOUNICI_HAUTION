<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Soiree
{
    private int $id;
    private string $nom;
    private string $thematique;
    private string $date;
    private string $horaire;
    private string $lieu;
    private array $spectacles= [];

    public function __construct(string $nom, string $thematique, string $date, string $horaire, string $lieu, array $spectacles= [])
    {
        $this->id = 0;
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->lieu = $lieu;
        $this->spectacles = $spectacles;
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

    public function addSpectacle(Spectacle $spectacle) : void
    {
        $this->spectacles[] = $spectacle;
    }

    public function removeSpectacle(int $index) : void
    {
        unset($this->spectacles[$index]);
    }

}