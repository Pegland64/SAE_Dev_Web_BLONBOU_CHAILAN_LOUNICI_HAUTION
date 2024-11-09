<?php

namespace nrv\net\action;

use nrv\net\action\Action;

class DefaultAction extends Action
{

    public function executeGET(): string
    {
        return "<p>Bienvenue sur le site de NRV.</p>";
    }

    public function executePOST(): string
    {
        return $this->executeGET();
    }
}