<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action
{

    public function execute(): string
    {
        $html = "";

        if (isset($_SESSION['user'])) {
            return "<div>Vous êtes déjà connecté en tant que " . htmlspecialchars($_SESSION['user']) . ".</div>";
        }

        if($this->http_method === 'GET'){
            $html = <<<HTML
                <form method="post" action="?action=signin">
                    <label>Email:
                    <input type="email" name="email" placeholder="email@example.com" required></label>
                    <label>Password:
                    <input type="password" name="passwd" required></label>
                    <button type="submit">Sign In</button>
                </form>
                HTML;
        } elseif($this->http_method === 'POST'){
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
            try {
                AuthnProvider::signin($email, $password);
                $html = "<div>Authentication successful. Welcome, $email!</div>";
            } catch (AuthnException $e) {
                $html = "<div>Error: " . $e->getMessage() . "</div>";
            }
        }
        return $html;
    }
}