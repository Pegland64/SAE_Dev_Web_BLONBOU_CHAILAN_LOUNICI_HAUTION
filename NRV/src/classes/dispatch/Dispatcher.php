<?php

namespace nrv\net\dispatch;

use nrv\net\action as act;

class Dispatcher
{
    private ?string $action = null;

    public function __construct()
    {
        // On détermine l'action à partir du paramètre GET 'action', avec 'default' comme valeur par défaut
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run() : void
    {
        $html = '';
        switch ($this->action) {
            case 'default':
                $html = "<p>Bienvenue sur NRV.net</p>";
                break;

            case 'display-spectacle':
                // Afficher les détails d'un spectacle
                $action = new act\DisplaySpectacleAction();
                $html = $action->execute();
                break;

            case 'liste-spectacles':
                // Afficher la liste de tous les spectacles
                $action = new act\SoireeAction();
                $html = $action->execute();
                break;

            case 'login':
                // Code pour la connexion (pas encore implémenté)
                $html = "<p>Page de connexion - Fonctionnalité en cours de développement.</p>";
                break;

            case 'add-spectacle':
                // Code pour ajouter un spectacle (pas encore implémenté)
                $html = "<p>Ajouter un spectacle - Fonctionnalité en cours de développement.</p>";
                break;
            
            case 'test':
                // Code pour tester des fonctionnalités (pas encore implémenté)
                $action =new  act\TestAction();
                $html = $action->execute();
                break;

            default:
                $html = "<p>Action inconnue : {$this->action}</p>";
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
    <div class="content">
        $html  
    </div>
</body>
</html>
HTML;
    }
}
