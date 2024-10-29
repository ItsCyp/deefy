<?php

namespace iutnc\deefy\exception;

class AuthnException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}