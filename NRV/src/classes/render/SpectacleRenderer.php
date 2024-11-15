<?php

namespace nrv\net\render;

use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;
use nrv\net\show\Spectacle;

/**
 * Classe SpectacleRenderer
 * Permet de générer des représentations HTML compactes ou complètes d'un spectacle.
 */
class SpectacleRenderer implements Renderer
{
    /** @var Spectacle $spectacle Instance du spectacle à rendre */
    private Spectacle $spectacle;

    /**
     * Constructeur.
     * Initialise le renderer avec une instance de Spectacle.
     *
     * @param Spectacle $spectacle Spectacle à rendre.
     */
    public function __construct(Spectacle $spectacle)
    {
        $this->spectacle = $spectacle;
    }

    /**
     * Génère une représentation HTML du spectacle selon le type spécifié.
     *
     * @param int $type Type de rendu (COMPACT ou FULL).
     * @return string Code HTML généré.
     */
    public function render(int $type): string
    {
        switch ($type) {
            case self::COMPACT:
                return $this->compact() . "<br>";
            case self::FULL:
                return $this->full() . "<br>";
            default:
                return "Type de rendu inconnu.";
        }
    }

    /**
     * Génère un rendu compact du spectacle.
     * Affiche des informations essentielles : titre, date, horaire, image, et liens pour les détails.
     *
     * @return string Code HTML compact.
     */
    private function compact(): string
    {
        $id = $this->spectacle->id_spectacle;
        $id_soiree = $this->spectacle->id_soiree;
        $image_url = $this->spectacle->images[0]->url ?? ''; // URL de la première image du spectacle
        $image_nom = $this->spectacle->images[0]->nom_image ?? ''; // Nom de la première image
        $horaire = $this->spectacle->horaire->format('H:i:s');
        $date = NrvRepository::getInstance()
            ->getSoireeById($this->spectacle->id_soiree)
            ->date->format('d/m/Y');
        $cookieName = "spectacle_id_$id";

        // Détermine le texte du bouton selon la présence d'un cookie
        $buttonText = isset($_COOKIE[$cookieName]) ? 'Retirer des Favoris' : 'Ajouter aux Favoris';

        return <<<HTML
    <div>
        <h3>
            Spectacle : {$this->spectacle->titre}
            <form method="POST" action=''>
                <button type="submit" name="spectacle_id" value="$id">$buttonText</button>
            </form>
        </h3>
        <p>Le <span class="dateDeco">{$date}</span> à <span class="dateDeco">{$horaire}</span></p>
        <img src="{$image_url}" alt="{$image_nom}" id="img_spectacle">
        <p><a href="?action=soiree&id_soiree={$id_soiree}">Voir la soirée ></a></p>
        <p><a href="?action=display-spectacle&id_spectacle={$id}">Voir les détails du spectacle ></a></p>
    </div>
HTML;
    }

    /**
     * Génère un rendu complet du spectacle.
     * Affiche toutes les informations, y compris les artistes, images, description, et vidéo.
     *
     * @return string Code HTML complet.
     */
    private function full(): string
    {
        $id = $this->spectacle->id_spectacle;

        // Génère les balises HTML pour les images du spectacle
        $images = '';
        foreach ($this->spectacle->images as $image) {
            $images .= "<img src='{$image->url}' alt='{$image->nom_image}' id='img_spectacle2'><br>";
        }

        // Génère la liste des artistes associés au spectacle
        $artistes = '<ul>';
        foreach ($this->spectacle->artistes as $artiste) {
            $artistes .= "<li>{$artiste->nom_artiste}</li>";
        }
        $artistes .= '</ul>';

        $date = NrvRepository::getInstance()
            ->getSoireeById($this->spectacle->id_soiree)
            ->date->format('d/m/Y');
        $horaire = $this->spectacle->horaire->format('H:i:s');
        $duree = $this->spectacle->duree->format('H:i:s');

        // Détermine le texte du bouton selon la présence d'un cookie
        $cookieName = "spectacle_id_$id";
        $buttonText = isset($_COOKIE[$cookieName]) ? 'Retirer des Favoris' : 'Ajouter aux Favoris';

        return <<<HTML
            <div>
                <h3>
                    Spectacle : {$this->spectacle->titre}
                    <form method="POST" action=''>
                        <button type="submit" name="spectacle_id" value="$id">$buttonText</button>
                    </form>
                </h3>
                <p>Artistes :</p>
                {$artistes}
                <p>Description : {$this->spectacle->description}</p>
                <p>Style : {$this->spectacle->style}</p>
                <p>Le {$date} à {$horaire}</p>
                <p>Durée : {$duree}</p>
                {$images}
                <iframe width="560" height="315" src="{$this->spectacle->video}" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin" 
                        allowfullscreen>
                </iframe>
                <br>
                <a href='?action=edit-spectacle&id_spectacle={$this->spectacle->id_spectacle}' class='btn btn-primary'>Modifier ce spectacle</a>
            </div>
HTML;
    }
}
