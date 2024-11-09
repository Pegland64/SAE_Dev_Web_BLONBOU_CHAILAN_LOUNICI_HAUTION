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
            case 1:
                return $this->compact() . "<br>";
            case 2:
                return $this->full() . "<br>";
            default:
                return "Type de rendu inconnu.";
        }
    }

    private function compact()
    {
        return <<<HTML
        <div>
            <h2>Spectacle : {$this->spectacle->titre}</h2>
            <p>Date : {$this->spectacle->date} - {$this->spectacle->horaire}</p>
            <img src="{$this->spectacle->image}" alt="{$this->spectacle->titre}">
        </div>
HTML;

    }

    private function full()
    {
        return <<<HTML
<div>
    <h2>Spectacle : {$this->spectacle->titre}</h2>
    <p>Artistes : {$this->spectacle->artiste}</p>
    <p>Description : {$this->spectacle->description}</p>
    <p>Style : {$this->spectacle->style}</p>
    <p>Durée : {$this->spectacle->duree} minutes</p>
    <img src="{$this->spectacle->image}" alt="{$this->spectacle->titre}">
</div>


HTML;
// implémentation d'une vidéo plutard
//        <video controls>
//        <source src="{$this->spectacle->video}" type="video/mp4">
//        Your browser does not support the video tag.
//    </video>

    }
}