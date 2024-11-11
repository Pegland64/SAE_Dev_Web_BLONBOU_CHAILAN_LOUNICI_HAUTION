<?php
namespace nrv\net\auth;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\repository\NrvRepository;
use nrv\net\user\User;

class AuthnProvider
{
    /**
     * Fonction de connexion sécurisée d'un utilisateur.
     * @param string $username Le nom d'utilisateur.
     * @param string $password Le mot de passe fourni.
     * @throws \Exception En cas d'erreur d'authentification ou de trop de tentatives.
     */
    public static function login(string $username, string $password) : void
    {

        // Initialisation du compteur de tentatives de connexion
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        // Vérifie si le nombre de tentatives de connexion a dépassé la limite
//        if ($_SESSION['login_attempts'] >= 5) {
//            throw new \Exception("Trop de tentatives de connexion. Veuillez réessayer plus tard.");
//        }

        // Récupère l'utilisateur par nom d'utilisateur depuis le dépôt
        $user = NrvRepository::getUserByUsername($username);

        // Vérifie si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user->password)) {
            // Mot de passe correct, on démarre une session sécurisée pour l'utilisateur
            $_SESSION['user'] = $user;  // Stocke l'utilisateur dans la session
            $_SESSION['login_attempts'] = 0;  // Réinitialise le compteur de tentatives en cas de succès

            // Sécurisation supplémentaire en régénérant l'ID de session après connexion réussie
            session_regenerate_id(true);

        } else {
            // Mot de passe incorrect ou utilisateur introuvable
            $_SESSION['login_attempts']++;  // Incrémente le compteur de tentatives
            throw new \Exception("Nom d'utilisateur ou mot de passe incorrect.");
        }
    }

    /**
     * Fonction de déconnexion de l'utilisateur.
     */
    public static function logout(): void
    {
        if (session_start() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Vérifie si un utilisateur est connecté.
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }
}
