<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\exception\InvalidPropertyValueException;

class Spectacle
{
    // Identifiant du spectacle
    private int $id_spectacle;

    // Titre du spectacle
    private string $titre;

    // Description du spectacle
    private string $description;

    // Style du spectacle
    private string $style;

    // URL de la vidéo du spectacle
    private string $video;

    // Horaire du spectacle
    private \DateTime $horaire;

    // Durée du spectacle
    private \DateTime $duree;

    // Liste des artistes participant au spectacle
    private array $artistes;

    // Liste des images associées au spectacle
    private array $images;

    // État du spectacle (confirmé ou annulé)
    private string $etat;

    // Identifiant de la soirée associée au spectacle
    private int $id_soiree;

    // Définir un spectacle avec le titre, la date, l'horaire, une image, l'artiste, le lieu, la description, le style, une vidéo
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

    // Méthode magique pour définir les propriétés privées
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new InvalidPropertyNameException("La propriété $name n'existe pas.");
        }
    }

    // Définit le titre du spectacle
    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    // Définit la description du spectacle
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    // Définit le style du spectacle
    public function setStyle(string $style): void
    {
        $this->style = $style;
    }

    // Définit l'identifiant du spectacle
    public function setIdSpectacle(int $id_spectacle) : void
    {
        $this->id_spectacle = $id_spectacle;
    }

    // Définit la liste des artistes participant au spectacle
    public function setArtistes(array $artistes) : void
    {
        $this->artistes = $artistes;
    }

    // Définit la liste des images associées au spectacle
    public function setImages(array $images) : void
    {
        $this->images = $images;
    }

    // Définit l'état du spectacle (confirmé ou annulé)
    public function setEtat(string $etat) : void
    {
        if($etat == "confirmé" || $etat == "annulé")
        {
            $this->etat = $etat;
        }else{
            throw new InvalidPropertyValueException("Erreur : La valeur $etat n'est pas valide pour l'état");
        }
    }

    // Définit l'identifiant de la soirée associée au spectacle
    public function setIdSoiree(int $id_soiree) : void
    {
        $this->id_soiree = $id_soiree;
    }

    // Définit la durée du spectacle
    public function setDuree(\DateTime $duree) : void
    {
        $this->duree = $duree;
    }

    // Définit l'horaire du spectacle
    public function setHoraire(\DateTime $horaire_debut)
    {
        $this->horaire = $horaire_debut;
    }
}