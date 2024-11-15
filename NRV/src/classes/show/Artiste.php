<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Artiste
{
    private int $id_artiste; // ID of the artist
    private string $nom_artiste; // Name of the artist
    private string $bio; // Biography of the artist

    // Constructor to initialize the artist's name and biography
    public function __construct($nom_artiste, $bio)
    {
        $this->nom_artiste = $nom_artiste;
        $this->bio = $bio;
    }

    // Magic getter method to access private properties
    public function __get(string $name) : mixed
    {
        if(property_exists($this, $name))
        {
            return $this->$name;
        }else{
            throw new InvalidPropertyNameException("Erreur : La propriété $name n'existe pas");
        }
    }

    // Method to set the artist's ID
    public function setIdArtiste(int $id_artiste) : void
    {
        $this->id_artiste = $id_artiste;
    }

}