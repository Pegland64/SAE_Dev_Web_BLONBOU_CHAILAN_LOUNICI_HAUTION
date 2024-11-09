<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

class ListeSpectaclesAction extends Action
{

    public function executeGET(): string
    {
        $spectacles = NrvRepository::getInstance()->getAllSpectacles();
        $html = "<ul>";
        foreach ($spectacles as $spectacle) {
            $renderer = new SpectacleRenderer($spectacle);
            $html .= "<li>" . $renderer->render(1) . "</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}