<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\exception\InvalidPropertyValueException;

class Spectacle
{
    private int $id_spectacle;
    private string $titre;
    private string $description;
    private string $style;
    private string $video;
    private \DateTime $horaire;
    private string $duree;
    private array $artistes;
    private array $images;
    private string $etat;
    private int $id_soiree;


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

    public function setIdSpectacle(int $id_spectacle) : void
    {
        $this->id_spectacle = $id_spectacle;
    }

    public function setArtistes(array $artistes) : void
    {
        $this->artistes = $artistes;
    }

    public function setImages(array $images) : void
    {
        $this->images = $images;
    }

    public function setEtat(string $etat) : void
    {
        if($etat == "confirmé" || $etat == "annulé")
        {
            $this->etat = $etat;
        }else{
            throw new InvalidPropertyValueException("Erreur : La valeur $etat n'est pas valide pour l'état");
        }
    }

    public function setIdSoiree(int $id_soiree) : void
    {
        $this->id_soiree = $id_soiree;
    }

}