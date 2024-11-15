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
            case self::COMPACT:
                return $this->compact() . "<br>";
            case self::FULL:
                return $this->full() . "<br>";
            default:
                return "Type de rendu inconnu.";
        }
    }

    private function compact(): string
    {
        $date = $this->soiree->date->format('d/m/Y');
        $horaire = $this->soiree->horaire->format('H:i:s');
        $duree = $this->soiree->duree->format('H:i:s');
        return <<<HTML
<div>
    <h2>Soirée : {$this->soiree->nom}</h2>
    <p>Le {$date} à {$horaire}</p>
    <p>Durée : {$duree}</p>
    <p>Lieu : {$this->soiree->lieu->nom}</p>
    <p>Tarif : {$this->soiree->tarif} €</p>
    <p><a href='?action=soiree&id_soiree={$this->soiree->id_soiree}'>En savoir plus ></a></p>
</div>
HTML;
    }

    private function full(): string
    {
        $date = $this->soiree->date->format('d/m/Y');
        $horaire = $this->soiree->horaire->format('H:i:s');
        $duree = $this->soiree->duree->format('H:i:s');
        $spectacles = '<ul>';
        foreach ($this->soiree->spectacles as $spectacle) {
            $renderer = new SpectacleRenderer($spectacle);
            $spectacles .= "<li>" . $renderer->render(Renderer::FULL) . "</li>";
        }
        $spectacles .= '</ul>';
        return <<<HTML
        <div>
            <h2>Soirée : {$this->soiree->nom}</h2>
            <p>Thématique : {$this->soiree->thematique}</p>
            <p>Le {$date} à {$horaire}</p>
            <p>Durée : {$duree}</p>
            <p>Lieu : {$this->soiree->lieu->nom}</p>
            <p>Tarif : {$this->soiree->tarif} €</p>
            <h3>Spectacles :</h3>
            {$spectacles}
        </div>
HTML;

    }

}