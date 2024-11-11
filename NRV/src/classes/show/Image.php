<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Image
{
    private string $url;
    private string $nom_image;
    private int $id_spectacle;

    public function __construct($url, $nom_image)
    {
        $this->url = $url;
        $this->nom_image = $nom_image;
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

    public function setIdSpectacle(int $id_spectacle) : void
    {
        $this->id_spectacle = $id_spectacle;
    }
}