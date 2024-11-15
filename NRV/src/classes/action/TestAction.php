<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;

// Classe pour tester l'affichage d'un spectacle
class TestAction{
    public function execute(): string
{
    // Récupère l'instance du repository
    $repo = NrvRepository::getInstance();
    // Récupère les données pour afficher un spectacle avec l'ID 1
    $spectacleAffichage = $repo->getDataForRenderSpectacle(1);

    // Génère le HTML pour l'affichage du spectacle
    $html = "<h2>Test</h2>";
    $html .= "<p>Test de l'affichage d'un spectacle</p>";
    $html .= "<div class='spectacle'>
                <h2>Titre : {$spectacleAffichage['titre']}</h2>
                <h3>Artiste : {$spectacleAffichage['artiste']}</h3>
                <p>Description : {$spectacleAffichage['description']}</p>
                <p>Style : {$spectacleAffichage['style']}</p>
                <p>Durée : {$spectacleAffichage['duree']}</p>";

    // Affichage de toutes les images
    foreach ($spectacleAffichage['images'] as $image) {
        $html .= "<div class='image'>
                    <img src='{$image['url']}' alt='{$image['alt']}' />
                  </div>";
    }

    // Affichage de l'extrait audio du spectacle
    $html .= "<audio controls>
                <source src='{$spectacleAffichage['extrait']}' type='audio/mpeg'>
                Votre navigateur ne supporte pas l'élément audio.
              </audio>
            </div>";

    // Retourne le HTML généré
    return $html;
}

}