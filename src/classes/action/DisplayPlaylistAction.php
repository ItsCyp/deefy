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
        $id = $_GET['id'] ?? 1;
        $repo = DeefyRepository::getInstance();
        try {
            Authz::checkPlaylistOwner($id);

            $playlist = $repo->findPlaylistById($id);
            $renderer = new render\AudioListRenderer($playlist);
            $html = $renderer->render(1);
        }catch (AccessControlException $e){
            $html = "Acces Denied: " . $e->getMessage();
        }catch (\Exception $e){
            $html = "Error: " . $e->getMessage();
        }
        return $html;
    }
}