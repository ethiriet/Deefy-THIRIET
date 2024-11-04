<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {

        if(!isset($_SESSION['user'])) {
            return "<div>Vous devez être connecté pour accéder à cette page.";
        }

        if ($this->http_method === 'GET') {
            $html = <<<HTML
                    <h2>Ajouter une Playlist</h2>
                    <form method="post" action="?action=add-playlist">
                    <label for="playlist_name">Nom Playlist :</label>
                    <input type="text" id="playlist_name" name="playlist_name" required>
                    <button type="submit">Creer la Playlist</button>
                    </form>
                    HTML;
        }
    
        elseif ($this->http_method === 'POST') {
            $name = filter_var($_POST['playlist_name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $r = DeefyRepository::getInstance();
            $_SESSION['playlist'] = $r->saveEmptyPlaylist(new lists\Playlist($name));
            if (isset($_SESSION['playlist'])) {
                $renderer = new \iutnc\deefy\render\AudioListRenderer($_SESSION['playlist']);
                $html = $renderer->render(1);
                $html .= '<a class="common-link" href="?action=add-track">Ajouter une piste</a>';
            } else {
                $html = "<div>Erreur : Une playlist existe déjà.</div>";
            }
        }
        return $html;
    }
}
