<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Lieu
{
    private string $nom;
    private string $adresse;
    private string $place_assises;
    private string $place_debout;
    private string $description;
    private array $images;

    public function __construct($nom, $adresse, $place_assises, $place_debout, $description)
    {
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->place_assises = $place_assises;
        $this->place_debout = $place_debout;
        $this->description = $description;
        $this->images = [];
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

    public function setImages(array $images) : void
    {
        $this->images = $images;
    }

}