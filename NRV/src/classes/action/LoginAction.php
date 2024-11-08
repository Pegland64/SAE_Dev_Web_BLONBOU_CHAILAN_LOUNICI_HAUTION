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

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            return "Connexion réussie !";
        } else {
            return "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
