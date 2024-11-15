<?php

namespace nrv\net\action;

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
                $html .= <<<HTML
                    <div class='spectacle'>
                        <h3>$spectacle->titre</h3>
                        <p>$spectacle->description</p>
                    </div><hr>
                    HTML;
            }
        } else {
            // Message à afficher si aucun spectacle n'a été sélectionné
            $html .= "<p>Aucun spectacle n'a été sélectionné pour le moment.</p>";
        }

        // Retourne le HTML généré
        return $html;
    }
}