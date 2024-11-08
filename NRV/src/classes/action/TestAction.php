<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;

class TestAction{
    public function execute(): string
{
    $repo = NrvRepository::getInstance();
    $spectacleAffichage = $repo->getDataForRenderSpectacle(1);
    
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

    $html .= "<audio controls>
                <source src='{$spectacleAffichage['extrait']}' type='audio/mpeg'>
                Votre navigateur ne supporte pas l'élément audio.
              </audio>
            </div>";

    return $html;
}

}