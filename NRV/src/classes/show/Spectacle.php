<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\exception\InvalidPropertyValueException;

class Spectacle
{
    private string $titre;
    private string $artiste;
    private string $description;
    private string $style;
    private string $image;
    private string $video;
    private string $date;
    private string $horaire;
    private int $duree;


    // définir un spectacle avec le titre, la date, l'horaire, une image, l'artiste, le lieu, la description, le style, une vidéo
    public function __construct(string $titre, string $date, string $horaire, string $image, string $artiste, string $description, string $style, string $video)
    {
        $this->titre = $titre;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->image = $image;
        $this->artiste = $artiste;
        $this->description = $description;
        $this->style = $style;
        $this->video = $video;
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

    public function setDuree(int $duree) : void
    {
        if($duree > 0) {
            $this->duree = $duree;
        }else{
            throw new InvalidPropertyValueException("Erreur : La durée doit être supérieure à 0");
        }
    }

}