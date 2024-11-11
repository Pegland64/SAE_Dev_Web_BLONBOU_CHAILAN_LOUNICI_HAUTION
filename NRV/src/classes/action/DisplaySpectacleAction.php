<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;

class DisplaySpectacleAction extends Action{

    public function execute(): string
    {
        if (!isset($_GET['id_spectacle'])) {
            return "Spectacle inconnu.";
        }
        $spectacle = NrvRepository::getInstance()->getSpectacleById((int)$_GET['id_spectacle']);
        $renderer = new SpectacleRenderer($spectacle);
        return $renderer->render(2);
    }
}