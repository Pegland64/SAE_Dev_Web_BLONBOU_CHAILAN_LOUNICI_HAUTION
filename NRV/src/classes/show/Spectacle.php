<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\exception\InvalidPropertyValueException;

class Spectacle
{
    private int $id;
    private string $titre;
    private string $description;
    private string $style;
    private string $video;
    private string $horaire;
    private string $duree;
    private array $artistes;
    private array $images;


    // définir un spectacle avec le titre, la date, l'horaire, une image, l'artiste, le lieu, la description, le style, une vidéo
    public function __construct($titre, $description, $video, $horaire, $duree, $style)
    {
        $this->titre = $titre;
        $this->description = $description;
        $this->video = $video;
        $this->horaire = $horaire;
        $this->duree = $duree;
        $this->style = $style;
        $this->images = [];
        $this->artistes = [];
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

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function setArtistes(array $artistes) : void
    {
        $this->artistes = $artistes;
    }

    public function setImages(array $images) : void
    {
        $this->images = $images;
    }

}