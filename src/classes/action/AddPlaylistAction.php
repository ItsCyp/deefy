<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists as lists;
use iutnc\deefy\repository as repo;

class AddPlaylistAction extends Action
{

    public function execute(): string
    {
        $html = "";

        // Vérifie si l'utilisateur est connecté
        if(!isset($_SESSION['user'])) {
            return "<div>Vous devez être connecté pour accéder à cette page. <a href='?action=signin'>Connexion</a></div>";
        }

        // Affiche le formulaire de création de playlist si la méthode HTTP est GET
        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <h2>Créer une playlist</h2>
                <form method="post" action="?action=add-playlist">
                    <label>Nom de la playlist :
                    <input type="text" name="name" placeholder="<name>"><label><br>
                    <button type="submit">Créer la playlist</button>
                </form>
                HTML;
        }
        // Traite le formulaire de création de playlist si la méthode HTTP est POST
        elseif ($this->http_method === 'POST') {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $r = repo\DeefyRepository::getInstance();
            $_SESSION['playlist'] = $r->saveEmptyPlaylist(new lists\Playlist($name));
            if (isset($_SESSION['playlist'])) {
                $renderer = new \iutnc\deefy\render\AudioListRenderer($_SESSION['playlist']);
                $html = $renderer->render(1);
                $html .= '<a class="common-link" href="?action=add-track">Ajouter une piste</a>';
            } else {
                $html = "<div>Erreur : Une playlist existe déjà.</div>";
            }
        }
        return $html;

    }
}