<?php

namespace nrv\net\action;

use nrv\net\render\SoireeRenderer;
use nrv\net\repository\NrvRepository;

// Classe pour afficher une soirée
class SoireeAction extends Action
{

    public function execute(): string
    {
        // Vérifie si l'ID de la soirée est défini dans les paramètres GET
        if (!isset($_GET['id_soiree'])) {
            return "Soirée inconnue.";
        }// Récupère la soirée par son ID
        $soiree = NrvRepository::getInstance()->getSoireeById((int)$_GET['id_soiree']);
        // Crée un renderer pour la soirée
        $renderer = new SoireeRenderer($soiree);
        // Retourne le rendu de la soirée
        return $renderer->render(2);
    }
}