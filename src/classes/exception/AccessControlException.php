<?php

namespace iutnc\deefy\exception;

class AccessControlException extends \Exception
{
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}