<?php

namespace nrv\net\dispatch;

use nrv\net\action as act;
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
        $restrictedActions = ['add-spectacle', 'display-spectacle'];

        if (in_array($this->action, $restrictedActions) && !$isAuthenticated) {
            $html = "<p>Veuillez vous <a href='?action=login'>connecter</a> pour accéder à cette fonctionnalité.</p>";
        } else {
            switch ($this->action) {
                case 'default':
                    // Utilisation d'un message statique pour l'accueil
                    $html = "<p>Bienvenue sur NRV.net</p>";
                    break;

                case 'display-spectacle':
                    $action = new act\DisplaySpectacleAction();
                    $html = $action->execute();
                    break;

                case 'liste-spectacles':
                    $action = new act\SoireeAction();
                    $html = $action->execute();
                    break;

                case 'login':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $action = new act\LoginAction();
                        $result = $action->execute();

                        if ($result === "Connexion réussie !") {
                            header('Location: index.php?action=default');
                            exit;
                        } else {
                            $html = "<p style='color: red;'>$result</p>";
                        }
                    }

                    $html .= $this->renderLoginForm();
                    break;

                case 'add-spectacle':
                    if ($isAuthenticated) {
                        $action = new act\AddSpectacleAction();
                        $html = $action->execute();
                    } else {
                        $html = "<p>Veuillez vous <a href='?action=login'>connecter</a> pour ajouter un spectacle.</p>";
                    }
                    break;

                case 'test':
                    $action = new act\TestAction();
                    $html = $action->execute();
                    break;

                default:
                    $html = "<p>Action inconnue : {$this->action}</p>";
                    break;
            }
        }

        $this->renderPage($html, $isAuthenticated);
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
    private function getAuthLinks(bool $isAuthenticated): string
    {
        // Liens de navigation basés sur l'état d'authentification
        if ($isAuthenticated) {
            return '<a href="?action=add-spectacle">Ajouter un spectacle</a>
                    <a href="logout.php">Déconnexion</a>';
        } else {
            return '<a href="?action=login">Connexion</a>';
        }
    }
    private function renderLoginForm(): string
    {
        return <<<HTML
        <form action="?action=login" method="post">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <br>
            <button type="submit">Se connecter</button>
        </form>
HTML;
    }
}
