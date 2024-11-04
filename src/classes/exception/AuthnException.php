<?php

namespace iutnc\deefy\exception;

use Exception;

class AuthnException extends Exception
{
    public function __construct($message = "Erreur Authentification")
    {
        parent::__construct($message);
    }
}