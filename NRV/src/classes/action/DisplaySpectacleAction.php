<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

class DisplaySpectacleAction extends Action{
    public function execute(): string
    {
        $html = '';
        if (!isset($_GET['id_spectacle'])) {
            return "Spectacle inconnu.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'])) {
            $spectacleId = intval($_POST['spectacle_id']);
            $cookieName = "spectacle_id_$spectacleId";

            if (isset($_COOKIE[$cookieName])) {
                setcookie($cookieName, '', time() - 3600, "/");
            } else {
                setcookie($cookieName, $spectacleId, time() + (7 * 24 * 60 * 60), "/");
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        $spectacle = NrvRepository::getInstance()->getSpectacleById((int)$_GET['id_spectacle']);
        $renderer = new SpectacleRenderer($spectacle);
        $html .= $renderer->render(2);

        $soireeSpectacle = NrvRepository::getInstance()->getSoireeById($spectacle->id_soiree);
        $spectacleParLieu = NrvRepository::getInstance()->getSpectaclesByLieu($soireeSpectacle->lieu->nom, $spectacle->id_spectacle);
        $html .= "<h2>Spectacles au même endroit</h2>";
        if (count($spectacleParLieu) >= 2) {
            $indiceDispo = range(0, count($spectacleParLieu) - 1);
            for ($i = 0; $i < 2; $i++) {
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParLieu[$indice]->id_spectacle . "'>" . $spectacleParLieu[$indice]->titre . $spectacleParLieu[$indice]->id_spectacle . "<br></a>";
            }
        } else if (count($spectacleParLieu) == 1) {
            $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParLieu[0]->id_spectacle . "'>" . $spectacleParLieu[0]->titre . $spectacleParLieu[0]->id_spectacle . "<br></a>";
        } else {
            $html .= "Aucun spectacle similaire";
        }

        $spectacleParStyle = NrvRepository::getInstance()->getSpectaclesByStyle($spectacle->style, $spectacle->id_spectacle);
        $html .= "<h2>Spectacles du même style</h2>";
        if (count($spectacleParStyle) >= 2) {
            $indiceDispo = range(0, count($spectacleParStyle) - 1);
            for ($i = 0; $i < 2; $i++) {
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParStyle[$indice]->id_spectacle . "'>" . $spectacleParStyle[$indice]->titre . $spectacleParStyle[$indice]->id_spectacle . "<br></a>";
            }
        } else if (count($spectacleParStyle) == 1) {
            $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParStyle[0]->id_spectacle . "'>" . $spectacleParStyle[0]->titre . $spectacleParStyle[0]->id_spectacle . "<br></a>";
        } else {
            $html .= "Aucun spectacle similaire";
        }

        $spectacleParDate = NrvRepository::getInstance()->getSpectaclesByDate(NrvRepository::getInstance()->getDateSpectacle($spectacle->id_spectacle), $spectacle->id_spectacle);
        $html .= "<h2>Spectacles du même jour</h2>";
        if (count($spectacleParDate) >= 2) {
            $indiceDispo = range(0, count($spectacleParDate) - 1);
            for ($i = 0; $i < 2; $i++) {
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParDate[$indice]->id_spectacle . "'>" . $spectacleParDate[$indice]->titre . $spectacleParDate[$indice]->id_spectacle . "<br></a>";
            }
        } else if (count($spectacleParDate) == 1) {
            $html .= "<a href='?action=display-spectacle&id_spectacle=" . $spectacleParDate[0]->id_spectacle . "'>" . $spectacleParDate[0]->titre . $spectacleParDate[0]->id_spectacle . "<br></a>";
        } else {
            $html .= "Aucun spectacle similaire";
        }

        return $html;
    }
}