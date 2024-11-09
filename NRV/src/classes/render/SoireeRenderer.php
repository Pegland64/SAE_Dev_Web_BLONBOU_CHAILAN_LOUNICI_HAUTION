<?php

namespace nrv\net\render;

use nrv\net\render\Renderer;
use nrv\net\show\Soiree;

class SoireeRenderer implements Renderer
{
    private Soiree $soiree;

    public function __construct(Soiree $soiree)
    {
        $this->soiree = $soiree;
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

    private function compact() : string
    {
        $html = "<div>";
        $html .= "<h2>Soirée : {$this->soiree->nom}</h2>";
        foreach($this->soiree->spectacles as $spectacle)
        {
            $renderer = new SpectacleRenderer($spectacle);
            $html .= $renderer->render(Renderer::COMPACT);
        }
        return $html . "</div>";
    }

    private function full() : string
    {
        return "<div><p>En cours de développement.</p></div>";
    }

}