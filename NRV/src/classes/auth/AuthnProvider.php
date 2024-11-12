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
    public static function login(string $username, string $password): void
    {

        // Initialisation du compteur de tentatives de connexion
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        // Vérifie si le nombre de tentatives de connexion a dépassé la limite
//        if ($_SESSION['login_attempts'] >= 5) {
//            throw new \Exception("Trop de tentatives de connexion. Veuillez réessayer plus tard.");
//        }

        try {
            // Récupère l'utilisateur par nom d'utilisateur depuis le dépôt
            $user = NrvRepository::getInstance()->getUserByUsername($username);

            if (password_verify($password, password_hash($user->password, PASSWORD_DEFAULT))) {
                $_SESSION['user'] = serialize($user);
                $_SESSION['login_attempts'] = 0;
            } else {
                $_SESSION['login_attempts']++;
                throw new AuthnException("Nom d'utilisateur ou mot de passe incorrect.");
            }
        } catch
        (\PDOException $e) {
            throw new \Exception("Erreur de base de données : " . $e->getMessage());
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

    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("User is not signed in.");
        }

        return unserialize($_SESSION['user']);
    }
}
