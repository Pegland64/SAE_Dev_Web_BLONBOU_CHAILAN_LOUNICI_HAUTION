<?php

namespace nrv\net\dispatch;

use nrv\net\action as act;
use nrv\net\action\ListeSoireeAction;
use nrv\net\action\SoireeAction;
use nrv\net\auth\AuthnProvider;

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

        // Vérifie l'authentification de l'utilisateur
        $isAuthenticated = AuthnProvider::isAuthenticated();

        // Définit les actions accessibles seulement aux utilisateurs connectés
        $restrictedActions = ['add-spectacle'];

        if (in_array($this->action, $restrictedActions) && !$isAuthenticated) {
            $html = "<p>Veuillez vous <a href='?action=login'>connecter</a> pour accéder à cette fonctionnalité.</p>";
        } else {
            switch ($this->action) {
                case 'default':
                    // Utilisation d'un message statique pour l'accueil
                    $html = "<p>Bienvenue sur NRV.net</p>";
                    break;

                case 'display-spectacle':
                    // Affiche un spectacle en fonction de l'ID fourni
                    $action = new act\DisplaySpectacleAction();
                    $html = $action->execute();
                    break;

                case 'liste-spectacles':
                    // Affiche la liste des spectacles
                    $action = new act\ListeSpectaclesAction();
                    $html = $action->execute();
                    break;

                case 'login':
                    $action = new act\LoginAction();
                    $html = $action->execute();
                    break;

                case 'register':
                    $action = new act\RegisterAction();
                    $html = $action->execute();
                    break;

                case 'logout':
                    $action = new act\DeconnexionAction();
                    $html = $action->execute();
                    break;

                case 'add-spectacle':
                    // Ajoute un spectacle
                    $action = new act\AddSpectacleAction();
                    $html = $action->execute();
                    break;

                case 'soiree':
                    // Affiche une soirée en fonction de l'ID fourni
                    $action = new SoireeAction();
                    $html = $action->execute();
                    break;

                case 'soirees':
                    // Affiche la liste des soirées
                    $action = new ListeSoireeAction();
                    $html = $action->execute();
                    break;

                case 'listes-spectales-favoris':
                    $action = new act\DisplayFavorisAction();
                    $html = $action->execute();
                    break;

                case 'add-soiree':
                    $action = new act\AddSoireeAction();
                    $html = $action->execute();
                    break;

                default:
                    $html = "<p>Action inconnue : {$this->action}</p>";
                    break;
            }
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div id="separateur">
            <h1>NRV.net</h1>
            <ul>
                <li><a href="?action=login">Connexion</a></li>
                <li><a href="?action=register">S'enregistrer</a></li>
                <li><a href="?action=logout">Déconnexion</a></li>
            </ul>
        </div>
        <ul>
            <li><a href="?action=default">Accueil</a></li>
            <li><a href="?action=soirees">Liste des soirées</a></li>
            <li><a href="?action=liste-spectacles">Liste des spectacles</a></li>
            <li><a href="?action=add-spectacle">Ajouter un spectacle</a></li>
            <li><a href="?action=add-soiree">Ajouter une soiree</a></li>
            <li><a href="?action=listes-spectales-favoris">voir favoris</a></li>
        </ul>
    </nav>
    
<!--    <nav>-->
<!--        <div class="nav-links">-->
<!--            <a href="?action=default">Accueil</a>-->
<!--            <a href="?action=login">Connexion</a>-->
<!--            <a href="?action=soirees">Liste des soirées</a>-->
<!--            <a href="?action=liste-spectacles">Liste des spectacles</a>-->
<!--            <a href="?action=add-spectacle">Ajouter un spectacle</a>-->
<!--        </div>-->
<!--    </nav>-->

    <div class="content">
        $html  
    </div>
</body>
</html>
HTML;
    }
}
