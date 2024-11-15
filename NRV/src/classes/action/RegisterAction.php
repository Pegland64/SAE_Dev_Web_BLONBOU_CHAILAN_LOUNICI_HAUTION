<?php

namespace nrv\net\action;

use nrv\net\auth\AuthnException;
use nrv\net\auth\AuthnProvider;

class RegisterAction extends Action{
    public function execute(): string{
        if (isset($_SESSION['user'])) {
            return "<div>Vous êtes déjà connecté en tant que " . unserialize($_SESSION['user'])->username . ".</div>";
        }
        if($this->http_method === 'GET'){
            return <<<HTML
                <form action="?action=register" method="post" id="registerForm">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" name="username" id="username" required>
                    <br>
                    <label for="email">Votre Email:</label>
                    <input type="email" name="email" id="email" required>
                    <label for="cemail">Confirmez votre email:</label>
                    <input type="email" name="cemail" id="email" required>
                    <br>
                    <label for="password">Mot de passe :</label>
                    <input type="password" name="password" id="password" required>
                    <label for="cpassword">Confirmez Mot de passe :</label>
                    <input type="password" name="cpassword" id="password" required>
                    <br>
                    <button type="submit">S'inscrire</button>
                </form>
            HTML;
        }else if ($this->http_method === 'POST' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['cpassword']) && isset($_POST['email']) && isset($_POST['cemail'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $cemail = $_POST['cemail'];
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];

            if($email !== $cemail){
                return "<div>Les noms d'utilisateur ne correspondent pas.</div>";
            }
            if($password !== $cpassword){
                return "<div>Les mots de passe ne correspondent pas.</div>";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($cemail, FILTER_VALIDATE_EMAIL)) {
                return "<div>L'email est invalide.</div>";
            }
            

            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // Authentifie l'utilisateur
                AuthnProvider::register($username,$email, $hash);
                
                return "<div>Bonjour, $username!</div>";
            } catch (AuthnException $e) {
                return "<div>Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}