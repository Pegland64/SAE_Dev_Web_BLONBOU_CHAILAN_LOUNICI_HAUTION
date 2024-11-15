<?php

namespace nrv\net\render;

use nrv\net\render\Renderer;
use nrv\net\show\Soiree;

/**
 * Classe SoireeRenderer
 * Permet de générer des représentations HTML compactes ou complètes d'une soirée.
 */
class SoireeRenderer implements Renderer
{
    /** @var Soiree $soiree Instance de la soirée à rendre */
    private Soiree $soiree;

    /**
     * Constructeur.
     * Initialise le renderer avec une instance de Soiree.
     *
     * @param Soiree $soiree Soirée à rendre.
     */
    public function __construct(Soiree $soiree)
    {
        $this->soiree = $soiree;
    }

    /**
     * Génère une représentation HTML de la soirée selon le type spécifié.
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
     * Génère un rendu compact de la soirée.
     * Affiche des informations essentielles : nom, date, horaire, durée, lieu, et un lien vers plus de détails.
     *
     * @return string Code HTML compact.
     */
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

    /**
     * Génère un rendu complet de la soirée.
     * Affiche toutes les informations, y compris les spectacles associés.
     *
     * @return string Code HTML complet.
     */
    private function full(): string
    {
        $date = $this->soiree->date->format('d/m/Y');
        $horaire = $this->soiree->horaire->format('H:i:s');
        $duree = $this->soiree->duree->format('H:i:s');

        // Liste des spectacles associés à la soirée
        $spectacles = '<ul>';
        foreach ($this->soiree->spectacles as $spectacle) {
            $renderer = new SpectacleRenderer($spectacle); // Utilisation de SpectacleRenderer pour rendre chaque spectacle
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
