<?php

namespace nrv\net\dispatch;

use nrv\net\action as act;
use nrv\net\action\ListeSoireeAction;
use nrv\net\action\SoireeAction;
use nrv\net\auth\AuthnProvider;
use nrv\net\auth\Authz;

/**
 * Classe Dispatcher
 * Gère la distribution des actions en fonction des paramètres reçus via l'URL.
 */
class Dispatcher
{
    /** @var string|null $action Action à exécuter (définie par le paramètre GET 'action') */
    private ?string $action = null;

    /**
     * Constructeur.
     * Détermine l'action demandée par l'utilisateur via le paramètre 'action' (ou 'default' par défaut).
     */
    public function __construct()
    {
        // On récupère l'action depuis les paramètres GET, avec 'default' comme fallback
        $this->action = $_GET['action'] ?? 'default';
    }

    /**
     * Exécute l'action demandée.
     * Vérifie les permissions, détermine l'action correspondante et affiche le résultat.
     *
     * @return void
     */
    public function run(): void
    {
        $html = '';

        // Vérifie si l'utilisateur est authentifié
        $isAuthenticated = AuthnProvider::isAuthenticated();

        // Liste des actions restreintes aux utilisateurs connectés
        $restrictedActions = ['add-spectacle', 'add-soiree'];

        // Vérification des droits d'accès
        if (in_array($this->action, $restrictedActions) && !$isAuthenticated) {
            // Message pour les utilisateurs non connectés
            $html = "<p>Veuillez vous <a href='?action=login'>connecter</a> pour accéder à cette fonctionnalité.</p>";
        } else {
            // Exécution des actions en fonction de l'action spécifiée
            switch ($this->action) {
                case 'default':
                    $action = new act\DefaultAction();
                    $html = $action->execute();
                    break;

                case 'display-spectacle':
                    $action = new act\DisplaySpectacleAction();
                    $html = $action->execute();
                    break;

                case 'liste-spectacles':
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
                    $action = new act\AddSpectacleAction();
                    $html = $action->execute();
                    break;

                case 'edit-spectacle':
                    $action = new act\EditSpectacleAction();
                    $html = $action->execute();
                    break;

                case 'soiree':
                    $action = new SoireeAction();
                    $html = $action->execute();
                    break;

                case 'afficher-users':
                    $action = new act\ListesUser();
                    $html = $action->execute();
                    break;

                case 'edit-user':
                    $action = new act\EditUser();
                    $html = $action->execute();
                    break;

                case 'delete-user':
                    $action = new act\SupprimerUser();
                    $html = $action->execute();
                    break;

                case 'soirees':
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
                    // Message en cas d'action inconnue
                    $html = "<p>Action inconnue : {$this->action}</p>";
                    break;
            }
        }

        // Affichage de la page avec le contenu généré
        $this->renderPage($html);
    }

    /**
     * Génère et affiche la page HTML avec le contenu spécifié.
     *
     * @param string $html Contenu principal à insérer dans la page.
     * @return void
     */
    private function renderPage(string $html): void
    {
        // Liens de connexion/déconnexion selon l'état de l'utilisateur
        $coDeco = isset($_SESSION['user'])
            ? '<li><a href="?action=logout">Déconnexion</a></li>'
            : '<li><a href="?action=login">Connexion</a></li><li><a href="?action=register">S\'enregistrer</a></li>';

        // Affichage de la section admin si l'utilisateur est autorisé
        $adminli = isset($_SESSION['user']) && (new Authz(unserialize($_SESSION['user'])))->checkStaffAdmin()
            ? '<li><a href="?action=afficher-users">Afficher les utilisateurs</a></li>'
            : ' ';

        // Template HTML
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NRV</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <nav>
        <div id="topNav">
            <h1>NRV.net</h1>
            <h2 id="hoverText">Oh la fête !</h2>
            <ul>
                $adminli
                $coDeco
            </ul>
        </div>
    </nav>
    
    <ul id="bottomNav">
        <li><a href="?action=default">Accueil</a></li>
        <li><a href="?action=soirees">Liste des soirées</a></li>
        <li><a href="?action=liste-spectacles">Liste des spectacles</a></li>
        <li><a href="?action=add-spectacle">Ajouter un spectacle</a></li>
        <li><a href="?action=add-soiree">Ajouter une soiree</a></li>
        <li><a href="?action=listes-spectales-favoris">Voir favoris</a></li>
    </ul>
    
    <div class="content">
        $html  
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const hoverText = document.getElementById('hoverText');
        let interval;

        

        hoverText.addEventListener('mouseenter', () => {
            interval = setInterval(() => {
                let random = Math.floor(Math.random() * 300);
                hoverText.style.color = "hsl(" + random + ", 100%, 50%)";
            }, 100); 
        });

        hoverText.addEventListener('mouseleave', () => {
            clearInterval(interval);
            hoverText.style.color = '';
        });
    });
</script>
</body>
</html>
HTML;
    }
}
