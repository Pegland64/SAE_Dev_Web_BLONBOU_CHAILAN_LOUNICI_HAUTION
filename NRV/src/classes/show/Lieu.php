<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Lieu
{
    // Nom du lieu
    private string $nom;

    // Adresse du lieu
    private string $adresse;

    // Nombre de places assises
    private string $place_assises;

    // Nombre de places debout
    private string $place_debout;

    // Description du lieu
    private string $description;

    // Liste des images associées au lieu
    private array $images;

    // Constructeur de la classe Lieu
    public function __construct($nom, $adresse, $place_assises, $place_debout, $description)
    {
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->place_assises = $place_assises;
        $this->place_debout = $place_debout;
        $this->description = $description;
        $this->images = [];
    }

    // Méthode magique pour accéder aux propriétés privées
    public function __get(string $name) : mixed
    {
        if(property_exists($this, $name))
        {
            return $this->$name;
        }else{
            throw new InvalidPropertyNameException("Erreur : La propriété $name n'existe pas");
        }
    }

    // Définit les images associées au lieu
    public function setImages(array $images) : void
    {
        $this->images = $images;
    }

}