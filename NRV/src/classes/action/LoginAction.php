<?php
namespace nrv\net\action;

use nrv\net\repository\NrvRepository;

class LoginAction extends Action
{
    public function execute(): string
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $repo = NrvRepository::getInstance();
        $user = $repo->getUserByUsername($username);

        // Vérifie si l'utilisateur existe et si le mot de passe est correct mais n'encode pas le mot de passe
        // TODO: Utiliser password_hash() pour stocker les mots de passe de manière sécurisée
        if ($user && $password === $user['password']) {
            $_SESSION['user'] = $user;
            return "Connexion réussie !";
        } else {
            return "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
