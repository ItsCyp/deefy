<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;
use \PDOException;


/**
 * Classe fournissant des méthodes pour l'authentification des utilisateurs.
 */
class AuthnProvider
{
    /**
     * Authentifie un utilisateur avec son email et son mot de passe.
     *
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @throws AuthnException Si les informations d'identification sont invalides ou s'il y a une erreur de base de données.
     */
    public static function signin(string $email, string $password): void
    {
        $repo = DeefyRepository::getInstance();
        try {
            $user = $repo->getUserByEmail($email);

            if ($user->verifyPassword($password)) {
                $_SESSION['user'] = serialize($user);
            } else {
                throw new AuthnException("Invalid credentials.");
            }
        } catch (\PDOException $e) {
            throw new AuthnException("Database error: " . $e->getMessage());
        }
    }

    /**
     * Enregistre un nouvel utilisateur avec son email et son mot de passe.
     *
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @throws AuthnException Si l'email existe déjà ou s'il y a une erreur de base de données.
     */
    public static function register(string $email, string $password): void
    {
//        if (!self::checkPasswordStrength($password, 10)) {
//            throw new AuthnException("Password does not meet the required strength.");
//        }

        $repo = DeefyRepository::getInstance();
        try {
            $repo->saveUser($email, $password);

            self::signin($email, $password);
        } catch (\PDOException $e) {
            throw new AuthnException("Database error: " . $e->getMessage());
        }
    }

    /**
     * Vérifie la force d'un mot de passe.
     *
     * @param string $pass Le mot de passe à vérifier.
     * @param int $minimumLength La longueur minimale requise pour le mot de passe.
     * @return bool Retourne true si le mot de passe répond aux critères de force, sinon false.
     */
    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $length = (strlen($pass) >= $minimumLength);  // longueur minimale
        $digit = preg_match("#[\d]#", $pass);        // au moins un digit
        $special = preg_match("#[\W]#", $pass);      // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass);       // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass);       // au moins une majuscule

        return $length && $digit && $special && $lower && $upper;
    }

    /**
     * Récupère l'utilisateur actuellement connecté.
     *
     * @return User L'utilisateur actuellement connecté.
     * @throws AuthnException Si aucun utilisateur n'est connecté.
     */
    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("User is not signed in.");
        }

        return unserialize($_SESSION['user']);
    }
}