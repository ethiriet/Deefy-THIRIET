<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;

class AddTrackAction extends Action
{
    public function execute(): string
    {
        
        if (!isset($_SESSION['playlist'])) {
            return "<div>Erreur : aucune playlist n'a été trouvée.</div>";
        }

        if ($this->http_method === 'GET') {
            return <<<HTML
            <form method="POST" enctype="multipart/form-data" action="?action=add-track">
                <label for="titre">Titre de la piste :</label>
                <input type="text" id="titre" name="titre" required><br>

                <label for="auteur">Auteur :</label>
                <input type="text" id="auteur" name="auteur"><br>

                <label for="date">Date de publication :</label>
                <input type="date" id="date" name="date"><br>

                <label for="genre">Genre :</label>
                <input type="text" id="genre" name="genre"><br>

                <label for="userfile">Fichier audio (.mp3) :</label>
                <input type="file" id="userfile" name="userfile" accept=".mp3" required><br><br>

                <input type="submit" value="Ajouter la piste">
            </form>
            HTML;
        }
        $file = $_FILES['userfile'];
            $fileExtension = substr($file['name'], -4);
            $fileType = $file['type'];

            if ($fileExtension !== '.mp3' || $fileType !== 'audio/mpeg') {
                return "<div>Erreur : Seuls les fichiers MP3 sont autorisés.</div>";
            }
        

        $newFileName = uniqid('audio_', true) . '.mp3';
        $uploadDir = dirname(__DIR__, 3) . '/music/';
        $uploadFilePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            return "<div>Erreur lors de l'upload du fichier.</div>";
        }

        if ($this->http_method === 'POST') {
            $titre = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
            $auteur = filter_var($_POST['auteur'] ?? 'Inconnu', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($_POST['date'] ?? 'Inconnu', FILTER_SANITIZE_SPECIAL_CHARS);
            $genre = filter_var($_POST['genre'] ?? 'Inconnu', FILTER_SANITIZE_SPECIAL_CHARS);

            $cheminMusique = 'music/' . $newFileName;
            $track = new PodcastTrack($titre, $cheminMusique);
            $track->setAuteur($auteur);
            $track->setDate($date);
            $track->setGenre($genre);


            $playlist = $_SESSION['playlist'];
            $playlist->ajouterPiste($track);
            $_SESSION['playlist'] = $playlist;

        
            $renderer = new AudioListRenderer($playlist);
            $playlistHtml = $renderer->render(1);
            $playlistHtml .= '<a href="?action=add-track">Ajouter encore une piste</a>';

            return $playlistHtml;
        }

        return "<div>Erreur : méthode HTTP non supportée.</div>";
    }
}
