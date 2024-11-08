<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;

class DisplaySpectacleAction extends Action{

    public function execute(): string
    {
        // Récupérer l'ID du spectacle depuis les paramètres GET
        $idSpectacle = isset($_GET['id_spectacle']) ? (int)$_GET['id_spectacle'] : null;

        // Vérifier si l'ID du spectacle est valide
        if ($idSpectacle === null) {
            return "<p>Erreur : ID de spectacle manquant.</p>";
        }

        // Récupérer l'instance du repository
        $repo = NrvRepository::getInstance();

        // Obtenir les détails du spectacle
        $spectacle = $repo->getSpectacleByIdSpectacle($idSpectacle);

        // Utiliser le renderer pour afficher le spectacle en mode LONG
        $renderer = new SpectacleRenderer($spectacle);
        $html = $renderer->render(Renderer::LONG);

        return $html;
    }
}