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
        $_SESSION['playlist_id'] = $_GET['id'];
        $id = $_SESSION['playlist_id'];
        $repo = DeefyRepository::getInstance();
        try {
            Authz::checkPlaylistOwner($id);

            $playlist = $repo->findPlaylistById($id);
            $_SESSION['playlist'] = $playlist;
            $renderer = new render\AudioListRenderer($playlist);
            $html = $renderer->render(1);
            $html .= '<a href="?action=add-track">Ajouter une piste</a>';
        }catch (AccessControlException $e){
            $html = "Acces Denied: " . $e->getMessage();
        }catch (\Exception $e){
            $html = "Error: " . $e->getMessage();
        }
        return $html;
    }
}