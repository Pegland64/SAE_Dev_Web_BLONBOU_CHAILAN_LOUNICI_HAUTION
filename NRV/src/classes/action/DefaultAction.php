<?php

namespace nrv\net\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        return <<<HTML
        <div style='position: relative; width: 100%; height: 100%;'>
            <div style='background-image: url("https://weezevent.com/wp-content/uploads/2019/03/01184934/organiser-soiree.jpeg"); background-size: cover; background-repeat: no-repeat; width: 100%; height: 100%; position: absolute; top: 0; left: 0; opacity: 0.7;'></div>
            <h1 style='color: black; text-align: center; padding-top: 1%; font-size: 10em; position: relative;'>Bienvenue au<br> NRV Festival</h1>
        </div>
        HTML;
    }
}