<?php

namespace nrv\net\action;

use nrv\net\render\Renderer;
use nrv\net\render\SpectacleRenderer;
use nrv\net\repository\NrvRepository;

class ListeSpectaclesAction extends Action
{

    public function execute(): string
    {
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



        $category = $_GET['category'] ?? null;
        $filter = $_GET['filter'] ?? 'all';

        $html = <<<HTML
        <form method="GET" action="">
            <input type="hidden" name="action" value="liste-spectacles">
            <button type="submit" name="category" value="date">Date</button>
            <button type="submit" name="category" value="lieu">Lieu</button>
            <button type="submit" name="category" value="style">Style</button>
        </form>
        HTML;

        if ($category) {
            $options = NrvRepository::getInstance()->getOptionsByCategory($category);
            $html .= '<form method="GET" action="">
                <input type="hidden" name="action" value="liste-spectacles">
                <input type="hidden" name="category" value="' . $category . '">';
            $html .= '<button type="submit" name="filter" value="all">Tous</button>';
            foreach ($options as $option) {
                $html .= '<button type="submit" name="filter" value="' . $option . '">' . $option . '</button>';
            }
            $html .= '</form>';
        }

        if ($filter) {
            $spectacles = NrvRepository::getInstance()->getFilteredSpectaclesByCategory($category, $filter);
            $html .= '<h2>Liste des spectacles :</h2><ul>';
            foreach ($spectacles as $spectacle) {
                $renderer = new SpectacleRenderer($spectacle);
                $html .= '<li>' . $renderer->render(1) . '</li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }
}