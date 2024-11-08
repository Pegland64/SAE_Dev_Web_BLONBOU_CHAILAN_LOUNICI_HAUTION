<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\render\Renderer;
use nrv\net\render\SpectacleRenderer;

class SoireeAction extends Action
{

    public function execute(): string
    {
        // Récupérer l'instance du repository
        $repo = NrvRepository::getInstance();

        // Obtenir tous les spectacles
        $spectacles = $repo->getAllSpectacle();

        // Générer le HTML pour afficher tous les spectacles
        $html = "<h2>Liste des Spectacles</h2><ul>";
        foreach ($spectacles as $spectacle) {
            // Utiliser le renderer pour chaque spectacle en mode SHORT
            $renderer = new SpectacleRenderer($spectacle);
            $html .= "<li><a href='?action=display-spectacle&id_spectacle={$spectacle->id}'>";
            $html .= $renderer->render(Renderer::SHORT); // Affichage succinct
            $html .= "</a></li>";
        }
        $html .= "</ul>";

        return $html;
    }
}