<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks;
use iutnc\deefy\render;
use iutnc\deefy\audio\tracks\AlbumTrack;

class AddTrackAction extends Action
{
    public function execute(): string
    {
        if(!isset($_SESSION['user'])) {
            return "<div>Vous devez être connecté pour accéder à cette page.</div>";
        }

        if (!isset($_SESSION['playlist'])) {
            return "<div>Aucune playlist sélectionnée.</div>";
        }
        if ($this->http_method === 'GET') {
            $html = <<<HTML
                <h2>Ajouter une piste à la playlist</h2>
                <form method="post" action="?action=add-track" enctype="multipart/form-data">
                    <label>Titre de la piste :
                    <input type="text" name="title" placeholder="Titre"><label><br>
                    <label>Artiste :
                    <input type="text" name="artiste" placeholder="Artiste"><label><br>
                    <label>Fichier audio :
                    <input type="file" name="userfile"><label><br>
                    <label>Durée (en secondes) :
                    <input type="number" name="duration" placeholder="Durée"><label><br>
                    <button type="submit">Ajouter la piste</button>
                </form>
                HTML;
        } elseif ($this->http_method === 'POST') {
            $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
            $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
            $artiste = filter_var($_POST['artiste'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  
            $fileInfo = pathinfo($_FILES['userfile']['name']);
            $fileExtension = strtolower($fileInfo['extension']);
            $fileType = $_FILES['userfile']['type'];

            if ($fileExtension === 'mp3' && $fileType === 'audio/mpeg') {
                $uploadDir = 'music/';
                $randomName = uniqid().'.mp3';
                $uploadFile = $uploadDir . $randomName;

                if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
                    $track = new AlbumTrack($title, $uploadFile, "Inconnu", 0, $duration);
                    $track->setArtiste($artiste);

                    $repository = \iutnc\deefy\repository\DeefyRepository::getInstance();
                    $id_track = $repository->saveTrack($track);

                    $repository->addTrackToPlaylist($id_track, $_SESSION['playlist_id']);

                    $playlist = $repository->findPlaylistById($_SESSION['playlist_id']);
                    $_SESSION['playlist'] = $playlist;


                    $renderer = new render\AudioListRenderer($playlist); 
                    $html = $renderer->render(1);
                    $html .= '<a class="common-link" href="?action=add-track">Ajouter encore une piste</a>';
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
