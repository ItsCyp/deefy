<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception as exception;

class User
{
    private int $id;
    private string $email;
    private string $password;
    private int $role;

    public function __construct(int $id, string $email, string $password, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
        throw new exception\InvalidPropertyNameException("Property $name does not exist.");
    }
}