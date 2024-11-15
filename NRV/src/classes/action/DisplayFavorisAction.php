<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

// Classe pour afficher les favoris
class DisplayFavorisAction extends Action
{
    // Méthode principale pour exécuter l'action
    public function execute(): string
    {
        // Récupère l'instance du repository
        $repo = NrvRepository::getInstance();
        $spectaclesSelectionnes = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'])) {
            $spectacleId = intval($_POST['spectacle_id']);
            $cookieName = "spectacle_id_$spectacleId";

            if (isset($_COOKIE[$cookieName])) {
                // Le cookie existe, donc on le supprime
                setcookie($cookieName, '', time() - 3600, "/"); // Expire immédiatement
            } else {
                // Le cookie n'existe pas, donc on le crée
                setcookie($cookieName, $spectacleId, time() + (7 * 24 * 60 * 60), "/"); // Expire dans 7 jours
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Parcourt tous les cookies pour trouver ceux qui correspondent aux spectacles favoris
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            if (strpos($cookieName, 'spectacle_id_') === 0) {
                // Extrait l'ID du spectacle à partir du nom du cookie
                $spectacleId = str_replace('spectacle_id_', '', $cookieName);
                $spectacles = $repo->getAllSpectacles();

                // Parcourt tous les spectacles pour trouver celui correspondant à l'ID extrait
                foreach ($spectacles as $spectacle) {
                    if ($spectacle->id_spectacle == $spectacleId) {
                        // Ajoute le spectacle à la liste des spectacles sélectionnés
                        $spectaclesSelectionnes[] = $spectacle;
                        break;
                    }
                }
            }
        }

        $html = "";

        // Génère le HTML pour afficher les spectacles sélectionnés
        if (!empty($spectaclesSelectionnes)) {
            foreach ($spectaclesSelectionnes as $spectacle) {
                $html .= "<div class='spectacle'>";
                $renderer = new SpectacleRenderer($spectacle);
                $html .= '<li>' . $renderer->render(1) . '</li>';
                $html.=  '</div><hr>';

            }
        } else {
            // Message à afficher si aucun spectacle n'a été sélectionné
            $html .= "<p>Aucun spectacle n'a été sélectionné pour le moment.</p>";
        }

        // Retourne le HTML généré
        return $html;
    }
}