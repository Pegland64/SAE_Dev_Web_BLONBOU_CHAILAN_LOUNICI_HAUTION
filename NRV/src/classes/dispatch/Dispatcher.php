<?php

namespace nrv\net\dispatch;

use nrv\net\action\DefaultAction;
use nrv\net\action\DisplaySpectacleAction;
use nrv\net\action\ListeSpectaclesAction;

class Dispatcher
{
    private ?string $action = null;

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run() : void
    {
        switch ($this->action) {
            case 'default':
                $action = new DefaultAction();
                break;
            case 'liste-spectacles':
                $action = new ListeSpectaclesAction();
                break;
            case 'display-spectacle':
                $action = new DisplaySpectacleAction();
                break;
            default:
                echo "Action inconnue.";
                break;
        }
        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html) : void
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NRV</title>
</head>
<body>
    <h1>NRV</h1>
    <ul>
        <li><a href="?action=default">Accueil</a></li>
        <li><a href="?action=liste-spectacles">Liste des spectacles</a></li>
        <li><a href="?action=display-spectacle">Afficher un spectacle</a></li>
    </ul>
    $html
</body>
</html>
HTML;

    }
}