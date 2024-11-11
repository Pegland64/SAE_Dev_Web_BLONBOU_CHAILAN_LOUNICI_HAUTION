<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Artiste
{
    private int $id_artiste;
    private string $nom_artiste;
    private string $bio;

    public function __construct($nom_artiste, $bio)
    {
        $this->nom_artiste = $nom_artiste;
        $this->bio = $bio;
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

    public function setIdArtiste(int $id_artiste) : void
    {
        $this->id_artiste = $id_artiste;
    }

}