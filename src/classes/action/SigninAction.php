<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action
{

    public function execute(): string
    {
        $html = "";

        // Vérifie si l'utilisateur est déjà connecté
        if (isset($_SESSION['user'])) {
            return "<div>Vous êtes déjà connecté en tant que " . unserialize($_SESSION['user'])->email . ".</div>";
        }

        // Affiche le formulaire de connexion si la méthode HTTP est GET
        if($this->http_method === 'GET'){
            $html = <<<HTML
                <h2>Connexion</h2>
                <form method="post" action="?action=signin">
                    <label>Email:
                    <input type="email" name="email" placeholder="email@example.com" required></label><br>
                    <label>Password:
                    <input type="password" name="passwd" required></label><br>
                    <button type="submit">Sign In</button>
                </form>
                HTML;
        }
        // Traite le formulaire de connexion si la méthode HTTP est POST
        elseif($this->http_method === 'POST'){
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
            try {
                // Authentifie l'utilisateur
                AuthnProvider::signin($email, $password);
                $html = "<div>Authentication successful. Welcome, $email!</div>";
            } catch (AuthnException $e) {
                $html = "<div>Error: " . $e->getMessage() . "</div>";
            }
        }
        return $html;
    }
}