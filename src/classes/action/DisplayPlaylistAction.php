<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authz;
use iutnc\deefy\render;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;

class DisplayPlaylistAction extends Action
{

    public function execute(): string
{
    $html = '';
    $repo = DeefyRepository::getInstance();

    try {
        // Récupère l'ID de la playlist et stocke en session
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id === null) {
            throw new \Exception("ID de playlist invalide.");
        }
        
        // Vérifie que l'utilisateur est propriétaire de la playlist
        Authz::checkPlaylistOwner($id);

        // Récupère et stocke la playlist en session
        $playlist = $repo->findPlaylistById($id);
        $_SESSION['playlist'] = $playlist;
        $_SESSION['playlist_id'] = $id;

        // Rendu de la playlist
        $renderer = new \iutnc\deefy\render\AudioListRenderer($playlist);
        $html = $renderer->render(1);
        $html .= '<a class="common-link" href="?action=add-track">Ajouter une piste</a>';
    } catch (AuthnException $e) {
        $html = "Accès refusé : " . $e->getMessage();
    } catch (\Exception $e) {
        $html = "Erreur : " . $e->getMessage();
    }
    
    return $html;
}
}