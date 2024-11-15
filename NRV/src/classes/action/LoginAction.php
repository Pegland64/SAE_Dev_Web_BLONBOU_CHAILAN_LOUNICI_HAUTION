<?php
namespace nrv\net\action;

use nrv\net\exception\AuthnException;
use nrv\net\auth\AuthnProvider;
use nrv\net\repository\NrvRepository;

// Classe pour gérer la connexion utilisateur
class LoginAction extends Action
{
    public function execute(): string
    {
        $html = '';

        // Vérifie si l'utilisateur est déjà connecté
        if (isset($_SESSION['user'])) {
            return "<div>Vous êtes déjà connecté en tant que " . unserialize($_SESSION['user'])->username . ".</div>";
        }

        // Gère la requête GET pour afficher le formulaire de connexion
        if($this->http_method === 'GET'){
            $html = <<<HTML
        <form action="?action=login" method="post" id="loginForm">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <br>
            <button type="submit">Se connecter</button>
        </form>
HTML;
        }else if ($this->http_method === 'POST'){
            // Gère la requête POST pour traiter la connexion
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $hash =password_verify($password, NrvRepository::getPassword($username));
                // Authentifie l'utilisateur
                AuthnProvider::login($username, $hash);
                $html = "<div>Bonjour, $username!</div>";
            } catch (AuthnException $e) {
                // Gère l'échec de l'authentification
                $html = "<div>Error: " . $e->getMessage() . "</div>";
            }


//            // Vérifie si l'utilisateur existe et si le mot de passe est correct mais n'encode pas le mot de passe
//            // TODO: Utiliser password_hash() pour stocker les mots de passe de manière sécurisée
//            if ($user && $password === $user['password']) {
//                $_SESSION['user'] = $user;
//                $html = "Connexion réussie !";
//            } else {
//                $html = "Nom d'utilisateur ou mot de passe incorrect.";
//            }
        }
        return $html;
    }
}
