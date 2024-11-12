<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;

class DisplaySpectacleAction extends Action{

    public function execute(): string
    {
        if (!isset($_GET['id_spectacle'])) {
            return "Spectacle inconnu.";
        }

         // permet l'ajout en favori
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
        $spectacle = NrvRepository::getInstance()->getSpectacleById((int)$_GET['id_spectacle']);
        $renderer = new SpectacleRenderer($spectacle);
        return $renderer->render(2);
    }
}