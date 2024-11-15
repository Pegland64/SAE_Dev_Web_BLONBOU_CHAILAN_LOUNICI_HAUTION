<?php

namespace nrv\net\action;


class DefaultAction extends Action
{
    public function execute(): string
    {
        return <<<HTML
        <div style='background-image: url("https://weezevent.com/wp-content/uploads/2019/03/01184934/organiser-soiree.jpeg"); background-size: cover; width: 100%; height: 1000px'>
            <h1 style='color: black; text-align: center; padding-top: 10%;font-size: 10em; '>Bienvenue NRV Festivale</h1>
        </div>
        HTML;
    }
}