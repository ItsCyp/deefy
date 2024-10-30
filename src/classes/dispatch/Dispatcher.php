<?php
declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action as act;

class Dispatcher
{
    private ?string $action = null;

    function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : 'default';
    }

    public function run(): void
    {
        switch ($this->action) {
            case 'default':
                $action = new act\DefaultAction();
                $html = $action->execute();
                break;
            case 'display-playlist':
                $action = new act\DisplayPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-playlist':
                $action = new act\AddPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-track':
                $action = new act\AddTrackAction();
                $html = $action->execute();
                break;
            case 'add-user':
                $action = new act\AddUserAction();
                $html = $action->execute();
                break;
            case 'signin':
                $action = new act\SigninAction();
                $html = $action->execute();
                break;
        }
        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        $playlistLink = '';
        if (isset($_SESSION['playlist'])) {
            $playlistId = $_SESSION['playlist_id'];
            $playlistLink = "<li><a href='?action=display-playlist&id={$playlistId}'>Afficher la playlist en session</a></li>";
        }

        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Deefy</title>
</head>
<body>
   <h1>Deefy</h1>
   <ul>
         <li><a href="?action=default">Accueil</a></li>
         <li><a href="?action=signin">Connexion</a></li>
         <li><a href="?action=add-user">Inscription</a></li>
         <li><a href="?action=add-playlist">Créer une playlist</a></li>
         <li><a href="?action=add-track">Ajouter une track dans la playlist</a></li>
         $playlistLink
    </ul>
    $html
</body>
</html>
HTML;
    }
}
