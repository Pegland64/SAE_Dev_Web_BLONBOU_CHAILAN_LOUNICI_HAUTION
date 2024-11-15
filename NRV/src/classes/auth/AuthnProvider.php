<?php

namespace nrv\net\auth;

use nrv\net\exception\AuthnException;
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
        if ($_SESSION['login_attempts'] >= 5) {
            throw new \Exception("Trop de tentatives de connexion. Veuillez réessayer plus tard.");
        }

        try {
            // Récupère l'utilisateur par nom d'utilisateur depuis le dépôt
            $user = NrvRepository::getInstance()->getUserByUsername($username);
            // Vérifie le mot de passe
            if (password_verify($password, NrVRepository::getInstance()->getHash($username))) {
                // Stocke l'utilisateur dans la session
                $_SESSION['user'] = serialize($user);
                $_SESSION['login_attempts'] = 0;
            } else {
                // Incrémente le compteur de tentatives de connexion
                $_SESSION['login_attempts']++;
                throw new AuthnException("Nom d'utilisateur ou mot de passe incorrect.");
            }
        } catch (\PDOException $e) {
            throw new \Exception("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Fonction d'enregistrement d'un nouvel utilisateur.
     * @param string $username Le nom d'utilisateur.
     * @param string $email L'email de l'utilisateur.
     * @param string $hash Le mot de passe haché.
     * @throws \Exception En cas d'erreur de base de données.
     */
    public static function register(string $username, string $email, string $hash): void
    {
        try {
            // Ajoute l'utilisateur dans le dépôt
            NrvRepository::getInstance()->addUser($username, $email, $hash);
            // Récupère l'utilisateur par nom d'utilisateur depuis le dépôt
            $user = NrvRepository::getInstance()->getUserByUsername($username);
            // Stocke l'utilisateur dans la session
            $_SESSION['user'] = serialize($user);
        } catch (\PDOException $e) {
            throw new \Exception("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Fonction de déconnexion de l'utilisateur.
     */
    public static function logout(): void
    {
        // Détruit la session
        session_destroy();
    }

    /**
     * Vérifie si un utilisateur est connecté.
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        // Vérifie si l'utilisateur est dans la session
        return isset($_SESSION['user']);
    }

    /**
     * Récupère l'utilisateur connecté.
     * @return User
     * @throws AuthnException Si l'utilisateur n'est pas connecté.
     */
    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("User is not signed in.");
        }

        // Retourne l'utilisateur désérialisé depuis la session
        return unserialize($_SESSION['user']);
    }
}