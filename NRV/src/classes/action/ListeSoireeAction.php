<?php

namespace nrv\net\action;

use nrv\net\render\SoireeRenderer;
use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

class ListeSoireeAction extends Action
{

    public function executeGET(): string
    {
        $soirees = NrvRepository::getInstance()->getAllSoirees();
        $html = "<h2>Liste des soirées : </h2>";
        $html .= "<ul>";
        foreach ($soirees as $soiree) {
            $renderer = new SoireeRenderer($soiree);
            $html .= "<li>{$renderer->render(1)}</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}