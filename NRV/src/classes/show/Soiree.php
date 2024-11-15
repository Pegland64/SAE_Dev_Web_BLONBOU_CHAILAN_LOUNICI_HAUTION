<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Soiree
{
    // Identifiant de la soirée
    private int $id_soiree;

    // Nom de la soirée
    private string $nom;

    // Thématique de la soirée
    private string $thematique;

    // Date de la soirée
    private \DateTime $date;

    // Horaire de début de la soirée
    private \DateTime $horaire;

    // Durée de la soirée
    private \DateTime $duree;

    // Lieu de la soirée
    private Lieu $lieu;

    // Tarif de la soirée
    private float $tarif;

    // Liste des spectacles associés à la soirée
    private array $spectacles;

    // Constructeur de la classe Soiree
    public function __construct($nom, $thematique, $date, $horaire, $lieu, $tarif)
    {
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->lieu = $lieu;
        $this->tarif = $tarif;
        $this->spectacles = [];
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

    // Définit l'identifiant de la soirée
    public function setIdSoiree(int $id_soiree) : void
    {
        $this->id_soiree = $id_soiree;
    }

    // Définit les spectacles associés à la soirée
    public function setSpectacles(array $spectacles) : void
    {
        $this->spectacles = $spectacles;
    }

    // Définit la durée de la soirée
    public function setDuree(\DateTime $duree) : void
    {
        $this->duree = $duree;
    }

}