<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;

class DisplayFavorisAction extends Action
{
    public function execute(): string
    {
        $repo = NrvRepository::getInstance();
        $spectaclesSelectionnes = [];
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            if (strpos($cookieName, 'spectacle_id_') === 0) {
                $spectacleId = str_replace('spectacle_id_', '', $cookieName);
                $spectacles = $repo->getAllSpectacles();
                foreach ($spectacles as $spectacle) {
                    if ($spectacle['id_spectacle'] == $spectacleId) {
                        $spectaclesSelectionnes[] = $spectacle;
                        break;
                    }
                }
            }
        }

        if (!empty($spectaclesSelectionnes)) {
            foreach ($spectaclesSelectionnes as $spectacle) {
                echo "<div class='spectacle'>";
                echo "<h3>" . htmlspecialchars($spectacle['titre']) . "</h3>";
                echo "<p>" . htmlspecialchars($spectacle['description']) . "</p>";
                echo "</div><hr>";
            }
        } else {
            echo "<p>Aucun spectacle n'a été sélectionné pour le moment.</p>";
        }
    }
}