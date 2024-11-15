<?php
namespace nrv\net\render;

use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;
use nrv\net\show\Spectacle;

class SpectacleRenderer implements Renderer
{

    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle)
    {
        $this->spectacle = $spectacle;
    }

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

    private function compact()
{
    $id = $this->spectacle->id_spectacle;
    $id_soiree = $this->spectacle->id_soiree;
    $image_url = $this->spectacle->images[0]->url ?? '';
    $image_nom = $this->spectacle->images[0]->nom_image ?? '';
    $horaire = $this->spectacle->horaire->format('H:i:s');
    $date = NrvRepository::getInstance()->getSoireeById($this->spectacle->id_soiree)->date->format('d/m/Y');
    $cookieName = "spectacle_id_$id";

    // Vérifier si le cookie existe
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


    private function full()
    {
        $id = $this->spectacle->id_spectacle;
        $images = '';
        foreach ($this->spectacle->images as $image) {
            $images .= "<img src='{$image->url}' alt='{$image->nom_image}'><br>";
        }

        $artistes = '<ul>';
        foreach ($this->spectacle->artistes as $artiste) {
            $artistes .= "<li>{$artiste->nom_artiste}</li>";
        }
        $artistes .= '</ul>';

        $date = NrvRepository::getInstance()->getSoireeById($this->spectacle->id_soiree)->date->format('d/m/Y');
        $horaire = $this->spectacle->horaire->format('H:i:s');
        $duree = $this->spectacle->duree->format('H:i:s');

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
                <iframe width="560" height="315" src="{$this->spectacle->video}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            HTML;

        // <p><a href="?action=soiree&id_soiree={$this->spectacle->id_soiree}">Detail de la soirée></a></p>
    }
}