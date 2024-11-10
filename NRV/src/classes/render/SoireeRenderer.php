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
        $html .= "<p>Thématique : {$this->soiree->thematique}</p>";
        $html .= "<p>Date : {$this->soiree->date->format('d/m/Y')}</p>";
        $html .= "<p>Horaire : {$this->soiree->horaire->format('H:i:s')}</p>";
        $html .= "<p>Lieu : {$this->soiree->lieu->nom}</p>";
        $html .= "<p>Tarif : {$this->soiree->tarif} €</p>";
        $html .= "<h3>Spectacles :</h3>";
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