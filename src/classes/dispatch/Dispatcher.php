<?php
declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action as act;

/**
 * Classe Dispatcher
 * Gère la distribution des actions en fonction des requêtes.
 */
class Dispatcher
{
    private ?string $action = null;

    /**
     * Constructeur de la classe Dispatcher.
     * Initialise l'action à partir des paramètres GET.
     */
    function __construct()
    {
        $this->action = isset($_GET['action']) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) : 'default';
    }

    /**
     * Exécute l'action en fonction de la valeur de $action.
     */
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

    /**
     * Affiche la page HTML avec le contenu généré par l'action.
     *
     * @param string $html Le contenu HTML à afficher.
     */
    private function renderPage(string $html): void
    {
        $playlistLink = '';
        if (isset($_SESSION['playlist'])) {
            $playlistId = htmlspecialchars($_SESSION['playlist_id'], ENT_QUOTES, 'UTF-8');
            $playlistLink = "<a href='?action=display-playlist&id={$playlistId}'>Afficher la playlist en session</a>";
        }

        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Deefy</title>
    <link rel="stylesheet" type="text/css" href="src/css/styles.css">
</head>
<body>
    <nav>
        <div class="nav-links">
            <a href="?action=default">Accueil</a>
            <a href="?action=signin">Connexion</a>
            <a href="?action=add-user">Inscription</a>
            <a href="?action=add-playlist">Créer une playlist</a>
            <a href="?action=add-track">Ajouter une track dans la playlist</a>
            $playlistLink
        </div>
    </nav>
    
    <h1>Deefy</h1>
    
    <div class="container">
        $html
    </div>
</body>
</html>
HTML;
    }
}