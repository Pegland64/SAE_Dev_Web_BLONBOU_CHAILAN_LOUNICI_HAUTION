<?php

namespace nrv\net\action;

use nrv\net\action\Action;
use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

class DisplaySpectacleAction extends Action
{

    public function executeGET(): string
    {
        if (!isset($_GET['id'])) {
            return "Spectacle inconnu.";
        }
        $spectacle = NrvRepository::getInstance()->getSpectacleById($_GET['id']);
        $renderer = new SpectacleRenderer($spectacle);
        return $renderer->render(2);
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}