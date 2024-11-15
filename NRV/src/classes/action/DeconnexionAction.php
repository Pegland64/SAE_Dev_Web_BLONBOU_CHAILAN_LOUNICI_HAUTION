<?php

namespace nrv\net\action;

use nrv\net\auth\AuthnProvider;

class DeconnexionAction extends Action
{
    public function execute(): string
    {
        $html = '';

        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<div>Vous n'êtes pas connecté.</div>";
        }

        // Affiche le formulaire de déconnexion si la méthode HTTP est GET
        if ($this->http_method === 'GET') {
            $html = <<<HTML
        <form action="?action=logout" method="post" id="deconnexionForm">
            <button type="submit">Se déconnecter</button>
        </form>
HTML;
        } else if ($this->http_method === 'POST') {
            // Déconnecte l'utilisateur si la méthode HTTP est POST
            AuthnProvider::logout();
            $html = "<div>Vous avez été déconnecté.</div>";
            header("Location: " . $_SERVER['REQUEST_URI']);
        }

        return $html;
    }
}