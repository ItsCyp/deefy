<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action
{

    public function execute(): string
    {
        $html = "";

        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <form method="post" action="?action=add-user">
                    <label>Nom :
                    <input type="text" name="name" placeholder="Nom" required><label><br>
                    <label>Email :
                    <input type="email" name="email" placeholder="Email" required><label><br>
                    <label>Âge :
                    <input type="number" name="age" placeholder="Âge" required><label><br>
                    <button type="submit">Connexion</button>
                </form>
                HTML;
        } elseif ($this->http_method === 'POST') {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $age = filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT);

            $html = "Nom: $name, Email: $email, Age: $age ans";
        }

        return $html;
    }
}