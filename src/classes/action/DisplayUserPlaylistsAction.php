<?php

namespace iutnc\deefy\action;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\repository\DeefyRepository;

class DisplayUserPlaylistsAction extends Action
{
    public function execute(): string
{
    $html = '';
    $repo = DeefyRepository::getInstance();

    try {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Vous devez vous connecter avec un compte pour accéder à vos playlists.");
        }   
        $user = AuthnProvider::getSignedInUser();

        $playlists = $repo->getUserPlaylists($user->__get('id'));

        foreach ($playlists as $id => $playlist) {
            $nom = htmlspecialchars($playlist->__get('nom'), ENT_QUOTES, 'UTF-8');
            $html .= "<li><a href='?action=display-playlist&id={$id}'>{$nom}</a></li>";
        }

        return "<ul>{$html}</ul>";
    } catch (AuthnException $e) {
        return "Erreur : " . $e->getMessage();
    }
}
}
