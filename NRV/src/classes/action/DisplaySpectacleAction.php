<?php

namespace nrv\net\action;

use nrv\net\render\SpectacleRenderer;
use nrv\net\render\Renderer;
use nrv\net\repository\NrvRepository;
//classe pour afficher un spectacle
class DisplaySpectacleAction extends Action{
    public function execute(): string
    {
        $html = '';
        // Vérifie si l'ID du spectacle est défini dans les paramètres GET
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
        // Récupère le spectacle par son ID
        $spectacle = NrvRepository::getInstance()->getSpectacleById((int)$_GET['id_spectacle']);
        // Crée un renderer pour le spectacle
        $renderer = new SpectacleRenderer($spectacle);
        $html.= $renderer->render(2);
        //affiche 2 spectacle aleatoire du meme lieu
        $soireeSpectacle = NrvRepository::getInstance()->getSoireeById($spectacle->id_soiree);
        $spectacleParLieu = NrvRepository::getInstance()->getSpectaclesByLieu($soireeSpectacle->lieu->nom, $spectacle->id_spectacle);
        $html .= "<h2>Spectacles au meme endroit</h2>";
        if(count($spectacleParLieu) >= 2){
            $indiceDispo = range(0, count($spectacleParLieu) - 1);
            for($i = 0; $i < 2; $i++){
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .="<a href='?action=display-spectacle&id_spectacle=".$spectacleParLieu[$indice]->id_spectacle."'>" .$spectacleParLieu[$indice]->titre.$spectacleParLieu[$indice]->id_spectacle . "<br></a>";
            }
        }else if(count($spectacleParLieu) == 1){
            $html .= "<a href='?action=display-spectacle&id_spectacle=".$spectacleParLieu[0]->id_spectacle."'>" .$spectacleParLieu[0]->titre.$spectacleParLieu[0]->id_spectacle . "<br></a>";
        }else{
            $html .= "Aucun spectacle similaire";
        }
        //affiche 2 spectacle aleatoire du meme style
        $spectacleParStyle = NrvRepository::getInstance()->getSpectaclesByStyle($spectacle->style, $spectacle->id_spectacle);
        $html .= "<h2>Spectacles du meme style</h2>";
        if(count($spectacleParStyle) >= 2){
            $indiceDispo = range(0, count($spectacleParStyle) - 1);
            for($i = 0; $i < 2; $i++){
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .="<a href='?action=display-spectacle&id_spectacle=".$spectacleParStyle[$indice]->id_spectacle."'>" .$spectacleParStyle[$indice]->titre.$spectacleParStyle[$indice]->id_spectacle . "<br></a>";
            }
        }else if(count($spectacleParStyle) == 1){
            $html .= "<a href='?action=display-spectacle&id_spectacle=".$spectacleParStyle[0]->id_spectacle."'>" .$spectacleParStyle[0]->titre.$spectacleParStyle[0]->id_spectacle . "<br></a>";
        }else{
            $html .= "Aucun spectacle similaire";
        }
        //affiche 2 spectacle aleatoire de la meme date
        $spectacleParDate = NrvRepository::getInstance()->getSpectaclesByDate(NrvRepository::getInstance()->getDateSpectacle($spectacle->id_spectacle), $spectacle->id_spectacle);
        $html .= "<h2>Spectacles du meme jour</h2>";
        if(count($spectacleParDate) >= 2){
            $indiceDispo = range(0, count($spectacleParDate) - 1);
            for($i = 0; $i < 2; $i++){
                $randomIndex = array_rand($indiceDispo);
                $indice = $indiceDispo[$randomIndex];
                $html .="<a href='?action=display-spectacle&id_spectacle=".$spectacleParDate[$indice]->id_spectacle."'>" .$spectacleParDate[$indice]->titre.$spectacleParDate[$indice]->id_spectacle . "<br></a>";
            }
        }else if(count($spectacleParDate) == 1){
            $html .= "<a href='?action=display-spectacle&id_spectacle=".$spectacleParDate[0]->id_spectacle."'>" .$spectacleParDate[0]->titre.$spectacleParDate[0]->id_spectacle . "<br></a>";
        }else{
            $html .= "Aucun spectacle similaire";
        }

        //affiche 
        return $html;
    }
}