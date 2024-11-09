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
        $id = $this->spectacle->id;
        $image = $this->spectacle->images[0] ?? '';
        return <<<HTML
        <div>
            <h2><a href="?action=display-spectacle&id={$id}">Spectacle : {$this->spectacle->titre}</a></h2>
            <p>Horaire : {$this->spectacle->horaire}</p>
            <img src="{$image}" alt="une image du spectacle">
        </div>
HTML;

    }

    private function full()
    {
        $images = '';
        foreach ($this->spectacle->images as $image) {
            $images .= "<img src='{$image}' alt='une image du spectacle'>";
        }

        $artistes = '';
        foreach ($this->spectacle->artistes as $artiste) {
            $artistes .= "<p> - {$artiste}</p>";
        }
        return <<<HTML
<div>
    <h2>Spectacle : {$this->spectacle->titre}</h2>
    <p>Artistes :</p>
    {$artistes}
    <p>Description : {$this->spectacle->description}</p>
    <p>Style : {$this->spectacle->style}</p>
    <p>Durée : {$this->spectacle->duree} minutes</p>
    <p>Horaire : {$this->spectacle->horaire}</p>
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