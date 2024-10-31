<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\AudioListRenderer;

class DefaultAction extends Action
{

    public function execute(): string
    {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ?action=signin');
            exit();
        }

        // Récupère l'utilisateur et ses playlists
        $user = unserialize($_SESSION['user']);
        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findPlaylistsByUser($user->id);

        // Génère le HTML pour afficher les playlists de l'utilisateur
        $html = "<div>Bonjour, " . $user->email . " !</div>";
        $html .= "<h2>Vos playlists :</h2><ul>";
        foreach ($playlists as $playlistId) {
            $playlist = $repo->findPlaylistById($playlistId['id_pl']);
            $html .= "<li><a class='common-link' href='?action=display-playlist&id={$playlistId['id_pl']}'>{$playlist->nom}</a></li>";
        }
        $html .= "</ul>";

        return $html;
    }
}