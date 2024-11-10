<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;

class ListeSoireeAction extends Action
{

    public function executeGET(): string
    {
        $soirees = NrvRepository::getInstance()->getAllSoirees();
        $html = "<h2>Liste des soirées : </h2>";
        $html .= "<ul>";
        foreach ($soirees as $soiree) {
            $html .= "<li><a href='?action=soiree&id_soiree=" . $soiree->id_soiree . "'>" . $soiree->nom . "</a></li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}