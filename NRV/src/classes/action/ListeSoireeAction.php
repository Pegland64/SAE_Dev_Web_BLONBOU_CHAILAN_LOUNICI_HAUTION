<?php

namespace nrv\net\action;

use nrv\net\action\Action;
use nrv\net\render\SoireeRenderer;
use nrv\net\repository\NrvRepository;

// Classe pour afficher la liste des soirées
class ListeSoireeAction extends Action
{
    // Méthode principale pour exécuter l'action
    public function execute(): string
    {
        // Récupère toutes les soirées depuis le repository
        $soirees = NrvRepository::getInstance()->getAllSoirees();
        $html = "<h2>Liste des soirées : </h2>";
        $html .= "<ul>";

        // Parcourt toutes les soirées pour générer le HTML correspondant
        foreach ($soirees as $soiree) {
            $renderer = new SoireeRenderer($soiree);
            $html .= "<li>{$renderer->render(1)}</li>";
        }
        $html .= "</ul>";
        // Retourne le HTML généré
        return $html;
    }
}