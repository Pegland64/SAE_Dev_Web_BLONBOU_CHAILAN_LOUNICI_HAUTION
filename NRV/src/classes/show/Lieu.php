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

    public function __construct($nom)
    {
        $this->nom = $nom;
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

}