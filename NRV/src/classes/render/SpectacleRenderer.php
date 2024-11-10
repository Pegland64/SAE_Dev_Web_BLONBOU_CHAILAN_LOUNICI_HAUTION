<?php

namespace nrv\net\render;

use nrv\net\render\Renderer;
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
        $image = $this->spectacle->images[0] ?? '';
        $horaire = $this->spectacle->horaire->format('H:i:s');
        return <<<HTML
        <div>
            <h3>Spectacle : {$this->spectacle->titre}</h3>
            <p>Horaire : {$horaire}</p>
            <img src="{$image}" alt="une image du spectacle">
            <p><a href="?action=display-spectacle&id_spectacle={$id}">En savoir plus</a></p>
        </div>
HTML;

    }

    private function full()
    {
        $images = '';
        foreach ($this->spectacle->images as $image) {
            $images .= "<img src='{$image}' alt='une image du spectacle'>";
        }

        $artistes = '<ul>';
        foreach ($this->spectacle->artistes as $artiste) {
            $artistes .= "<li>{$artiste}</li>";
        }
        $artistes .= '</ul>';

        $horaire = $this->spectacle->horaire->format('H:i:s');
        return <<<HTML
<div>
    <h3>Spectacle : {$this->spectacle->titre}</h3>
    <p>Artistes :</p>
    {$artistes}
    <p>Description : {$this->spectacle->description}</p>
    <p>Style : {$this->spectacle->style}</p>
    <p>Durée : {$this->spectacle->duree} minutes</p>
    <p>Horaire : {$horaire}</p>
    {$images}
</div>
HTML;
// implémentation d'une vidéo plutard
//        <video controls>
//        <source src="{$this->spectacle->video}" type="video/mp4">
//        Your browser does not support the video tag.
//    </video>

    }
}