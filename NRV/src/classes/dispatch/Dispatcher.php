<?php

namespace nrv\net\dispatch;

use nrv\net\action as act;

class Dispatcher
{
    private ?string $action = null;

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run() : void
    {
        $html = '';
        switch ($this->action) {
            case 'default' :
                break;
            case 'display-spectacle' :
                $action = new act\DisplaySpectacleAction();
                $html = $action->execute();
                break;
            case 'liste-spectacles' :
                break;
            case 'login' :
                break;
            case 'add-spectacle' :
                break;

        }
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
    <h1>NRV.net</h1>
    <nav>
        <div class="nav-links">
            <a href="?action=default">Accueil</a>
            <a href="?action=display-spectacle">Afficher spectacle</a>
            <a href="?action=add-spectacle">Ajouter un spectacle</a>
            <a href="?action=liste-spectacles">Liste des spectacles</a>
            <a href="?action=login">Connexion</a>
            
        </div>
    </nav>
    $html  
   
</body>
</html>
HTML;

    }
}