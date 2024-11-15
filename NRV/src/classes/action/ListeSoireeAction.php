<?php

namespace nrv\net\action;

use nrv\net\render\SoireeRenderer;
use nrv\net\repository\NrvRepository;

class ListeSoireeAction extends Action
{

    public function execute(): string
    {
        // Récupère toutes les soirées depuis le dépôt
        $soirees = NrvRepository::getInstance()->getAllSoirees();

        // Début de la construction du HTML pour l'affichage de la liste des soirées
        $html = "<div class='affichageListe' id='liste-soirees'>";
        $html .= "<h2><span class='listeTitre'>Liste des soirées :</span></h2>";
        $html .= "<ul>";

        // Parcourt chaque soirée et utilise le renderer pour générer le HTML
        foreach ($soirees as $soiree) {
            $renderer = new SoireeRenderer($soiree);
            $html .= "<li>{$renderer->render(1)}</li>";
        }

        // Termine la construction du HTML
        $html .= "</ul></div>";

        // Retourne le HTML généré
        return $html;
    }
}