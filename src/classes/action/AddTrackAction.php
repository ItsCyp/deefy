<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks as tracks;
use iutnc\deefy\render as render;

class AddTrackAction extends Action
{
    public function execute(): string
    {
        $html = "";

        if (!isset($_SESSION['playlist'])) {
            return "<div>Erreur : aucune playlist n'a été trouvée.</div>";
        }

        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <h2>Ajouter une piste à la playlist</h2>
                <form method="post" action="?action=add-track" enctype="multipart/form-data">
                    <label>Titre de la piste :
                    <input type="text" name="title" placeholder="Titre"><label><br>
                    <label>Fichier audio :
                    <input type="file" name="userfile"><label><br>
                    <label>Durée (en secondes) :
                    <input type="number" name="duration" placeholder="Durée"><label><br>
                    <button type="submit">Ajouter la piste</button>
                </form>
                HTML;
        } elseif ($this->http_method === 'POST') {
            $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);

            // Vérifier si le fichier est uploadé
            $fileInfo = pathinfo($_FILES['userfile']['name']);
            $fileExtension = strtolower($fileInfo['extension']);
            $fileType = $_FILES['userfile']['type'];

            if ($fileExtension === 'mp3' && $fileType === 'audio/mpeg') {
                $uploadDir = 'audio/';
                $randomName = uniqid().'.mp3';
                $uploadFile = $uploadDir . $randomName;

                if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
                    $track = new tracks\AlbumTrack($title, $uploadFile, "Inconnu", 0, $duration);

                    // Enregistrer la piste dans la base de données
                    $repository = \iutnc\deefy\repository\DeefyRepository::getInstance();
                    $id_track = $repository->saveTrack($track);

                    // Ajouter la piste à la playlist
                    $repository->addTrackToPlaylist($id_track, $_SESSION['playlist_id']);

                    $playlist = $repository->findPlaylistById($_SESSION['playlist_id']);
                    $_SESSION['playlist'] = $playlist;


                    $renderer = new render\AudioListRenderer($playlist);
                    $html = $renderer->render(1);
                    $html .= '<a href="?action=add-track">Ajouter encore une piste</a>';
                } else {
                    return "<div>Erreur : impossible de télécharger le fichier.</div>";
                }
            } else {
                return "<div>Erreur : fichier non valide. Seuls les fichiers .mp3 sont acceptés.</div>";
            }
        }

        return $html;
    }
}
