<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

/**
 * Class Image
 * Représente une image associée à un spectacle.
 */
class Image
{
    private string $url;
    private string $nom_image;
    private int $id_spectacle;

    /**
     * Constructeur de la classe Image.
     *
     * @param string $url L'URL de l'image.
     * @param string $nom_image Le nom de l'image.
     */
    public function __construct($url, $nom_image)
    {
        $this->url = $url;
        $this->nom_image = $nom_image;
    }

    /**
     * Méthode magique pour accéder aux propriétés privées.
     *
     * @param string $name Le nom de la propriété.
     * @return mixed La valeur de la propriété.
     * @throws InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function __get(string $name) : mixed
    {
        if(property_exists($this, $name))
        {
            return $this->$name;
        }else{
            throw new InvalidPropertyNameException("Erreur : La propriété $name n'existe pas");
        }
    }

    /**
     * Définit l'identifiant du spectacle associé à l'image.
     *
     * @param int $id_spectacle L'identifiant du spectacle.
     */
    public function setIdSpectacle(int $id_spectacle) : void
    {
        $this->id_spectacle = $id_spectacle;
    }
}