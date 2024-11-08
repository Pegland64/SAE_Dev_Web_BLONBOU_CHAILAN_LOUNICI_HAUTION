<?php

namespace nrv\net\action;
use nrv\net\action\Action;
use nrv\net\show\Spectacle;
use nrv\net\render\SpectacleRenderer;

class DisplaySpectacleAction extends Action{

    public function execute(): string
    {
        $s = new Spectacle("title", "artist", "description", "style", "duration", "image", "extrait");
        $r = new SpectacleRenderer($s);
        return $r->render(1);
    }
}