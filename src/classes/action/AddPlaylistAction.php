<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists as lists;
use iutnc\deefy\repository as repo;

class AddPlaylistAction extends Action
{

    public function execute(): string
    {
        $html = "";

        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <h2>Créer une playlist</h2>
                <form method="post" action="?action=add-playlist">
                    <label>Nom de la playlist :
                    <input type="text" name="name" placeholder="<name>"><label><br>
                    <button type="submit">Créer la playlist</button>
                </form>
                HTML;
        } elseif ($this->http_method === 'POST') {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $r = repo\DeefyRepository::getInstance();
            $_SESSION['playlist'] = $r->saveEmptyPlaylist(new lists\Playlist($name));
            if (isset($_SESSION['playlist'])) {
                $renderer = new \iutnc\deefy\render\AudioListRenderer($_SESSION['playlist']);
                $html = $renderer->render(1);
                $html .= '<a href="?action=add-track">Ajouter une piste</a>';
            } else {
                $html = "<div>Erreur : Une playlist existe déjà.</div>";
            }
        }
        return $html;

    }
}