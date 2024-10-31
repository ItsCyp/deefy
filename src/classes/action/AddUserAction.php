<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class AddUserAction extends Action
{

    public function execute(): string
    {
        $html = "";

        // Affiche le formulaire d'inscription si la méthode HTTP est GET
        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <h2>Inscription</h2>
                <form method="post" action="?action=add-user">
                    <label>Email:
                    <input type="email" name="email" placeholder="email@example.com" required></label><br>
                    <label>Password:
                    <input type="password" name="passwd" required></label><br>
                    <label>Confirm Password:
                    <input type="password" name="confirm_passwd" required></label><br>
                    <button type="submit">Register</button>
                </form>
                HTML;
        }
        // Traite le formulaire d'inscription si la méthode HTTP est POST
        elseif ($this->http_method === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
            $confirm_password = filter_input(INPUT_POST, 'confirm_passwd', FILTER_SANITIZE_STRING);

            // Vérifie si les mots de passe correspondent
            if ($password !== $confirm_password) {
                $html = "<div>Error: Passwords do not match.</div>";
            } else {
                try {
                    // Enregistre l'utilisateur
                    AuthnProvider::register($email, $password);
                    $html = "<div>Registration successful. Welcome, $email!</div>";
                } catch (AuthnException $e) {
                    $html = "<div>Error: " . $e->getMessage() . "</div>";
                }
            }
        }

        return $html;
    }
}