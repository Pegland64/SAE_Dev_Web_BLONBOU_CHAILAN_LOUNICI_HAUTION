<?php

namespace nrv\net\action;

use nrv\net\render\SoireeRenderer;
use nrv\net\repository\NrvRepository;

class SoireeAction extends Action
{

    public function executeGET(): string
    {
        if (!isset($_GET['id_soiree'])) {
            return "Soirée inconnue.";
        }
        $soiree = NrvRepository::getInstance()->getSoireeById((int)$_GET['id_soiree']);
        $renderer = new SoireeRenderer($soiree);
        return $renderer->render(2);
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}