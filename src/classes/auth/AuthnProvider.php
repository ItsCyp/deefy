<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;
use \PDOException;

class AuthnProvider
{
    public static function signin(string $email, string $password): void
    {
        $repo = DeefyRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare('SELECT passwd FROM user WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['passwd'])) {
                $_SESSION['user'] = $email;
            } else {
                throw new AuthnException("Invalid credentials.");
            }
        } catch (\PDOException $e) {
            throw new AuthnException("Database error: " . $e->getMessage());
        }
    }

    public static function register(string $email, string $password): void
    {
        if (!self::checkPasswordStrength($password, 10)) {
            throw new AuthnException("Password does not meet the required strength.");
        }

        $repo = DeefyRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare('SELECT email FROM user WHERE email = :email');
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                throw new AuthnException("An account with this email already exists.");
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $repo->getPdo()->prepare('INSERT INTO user (email, passwd, role) VALUES (:email, :passwd, 1)');
            $stmt->execute(['email' => $email, 'passwd' => $hashed_password]);

            $_SESSION['user'] = $email;
        } catch (\PDOException $e) {
            throw new AuthnException("Database error: " . $e->getMessage());
        }
    }

    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $length = (strlen($pass) >= $minimumLength);  // longueur minimale
        $digit = preg_match("#[\d]#", $pass);        // au moins un digit
        $special = preg_match("#[\W]#", $pass);      // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass);       // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass);       // au moins une majuscule

        return $length && $digit && $special && $lower && $upper;
    }
}