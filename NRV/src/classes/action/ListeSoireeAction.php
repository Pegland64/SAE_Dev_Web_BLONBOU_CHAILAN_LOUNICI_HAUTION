<?php

namespace nrv\net\action;

use nrv\net\action\Action;
use nrv\net\render\SoireeRenderer;
use nrv\net\repository\NrvRepository;

class ListeSoireeAction extends Action
{

    public function execute(): string
    {
        $soirees = NrvRepository::getInstance()->getAllSoirees();
        $html = "<div class='affichageListe' id='liste-soirees'>";
        $html .= "<h2><span class='listeTitre'>Liste des soirées :</span></h2>";
        $html .= "<ul>";
        foreach ($soirees as $soiree) {
            $renderer = new SoireeRenderer($soiree);
            $html .= "<li>{$renderer->render(1)}</li>";
        }
        $html .= "</ul></div>";
        return $html;
    }
}