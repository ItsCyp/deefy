<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception as exception;

/**
 * Classe User
 * Représente un utilisateur avec un identifiant, un email, un mot de passe et un rôle.
 */
class User
{
    private int $id;
    private string $email;
    private string $password;
    private int $role;

    /**
     * Constructeur de la classe User.
     *
     * @param int $id L'identifiant de l'utilisateur.
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @param int $role Le rôle de l'utilisateur.
     */
    public function __construct(int $id, string $email, string $password, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Méthode magique pour accéder aux propriétés privées.
     *
     * @param string $name Le nom de la propriété.
     * @return mixed La valeur de la propriété.
     * @throws exception\InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
        throw new exception\InvalidPropertyNameException("Property $name does not exist.");
    }

    /**
     * Vérifie si le mot de passe fourni correspond au mot de passe de l'utilisateur.
     *
     * @param string $password Le mot de passe à vérifier.
     * @return bool Retourne true si le mot de passe est correct, sinon false.
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}