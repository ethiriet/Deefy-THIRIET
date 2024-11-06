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
        $html = '';
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
                $action = new act\SignInAction();
                $html = $action->execute();
                break;
            case 'display-user-playlists':
                $action = new act\DisplayUserPlaylistsAction();
                $html = $action->execute();
                break;
        }
        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        $displayPlaylist = '';
        if (isset($_SESSION['playlist']) && isset($_SESSION['playlist_id'])) {
            $id = htmlspecialchars((string)$_SESSION['playlist_id'], ENT_QUOTES, 'UTF-8');
            $displayPlaylist = "<li><a href='?action=display-playlist&id={$id}'>Afficher la playlist en session</a></li>";
        }
        echo <<<HEAD
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Deefy</title>
    <link rel="stylesheet" type="text/css" href="src/css/styles.css">
</head>
<body>
    <header>
        <h1>Deefy</h1>
        <nav>
            <ul>
                <li><a href="?action=default">Accueil</a></li>
                <li><a href="?action=add-user">Inscription</a></li>
                <li><a href="?action=signin">Connexion</a></li>
                <li><a href="?action=add-playlist">Créer une playlist</a></li>
                <li><a href="?action=add-track">Ajouter une piste à la playlist</a></li>
                <li><a href="?action=display-user-playlists">Mes playlists</a></li>
                $displayPlaylist
            </ul>
        </nav>
    </header>
    <main>
        $html
    </main>
</body>
</html>
HEAD;
    }
}

