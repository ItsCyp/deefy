<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authz;
use iutnc\deefy\render as render;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AccessControlException;

class DisplayPlaylistAction extends Action
{

    public function execute(): string
    {
        $html = '';
        // Récupère l'identifiant de la playlist depuis les paramètres GET et le stocke dans la session
        $_SESSION['playlist_id'] = $_GET['id'];
        $id = $_SESSION['playlist_id'];
        $repo = DeefyRepository::getInstance();
        try {
            // Vérifie si l'utilisateur est propriétaire de la playlist
            Authz::checkPlaylistOwner($id);

            // Récupère la playlist depuis le dépôt et la stocke dans la session
            $playlist = $repo->findPlaylistById($id);
            $_SESSION['playlist'] = $playlist;
            $renderer = new render\AudioListRenderer($playlist);
            // Génère le HTML pour afficher la playlist
            $html = $renderer->render(1);
            $html .= '<a class="common-link" href="?action=add-track">Ajouter une piste</a>';
        } catch (AccessControlException $e) {
            // Gère les exceptions d'accès
            $html = "Acces Denied: " . $e->getMessage();
        } catch (\Exception $e) {
            // Gère les autres exceptions
            $html = "Error: " . $e->getMessage();
        }
        return $html;
    }
}