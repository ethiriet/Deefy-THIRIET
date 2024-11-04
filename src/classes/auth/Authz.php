<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class Authz
{
    /**
     * Vérifie si l'utilisateur connecté a le rôle requis
     * 
     * @param int $expectedRole Rôle requis
     * @throws AuthnException Si l'utilisateur n'a pas le rôle attendu
     */
    public static function checkRole(int $expectedRole): void
    {
        $user = AuthnProvider::getSignedInUser();
        
        if ($user->role >= $expectedRole) {
            throw new AuthnException("Accès refusé : rôle insuffisant.");
        }
    }

     /**
     * Vérifie si l'utilisateur est le propriétaire de la playlist ou a le rôle ADMIN
     * 
     * @param int $playlistId ID de la playlist
     * @throws AuthnException Si l'utilisateur n'est ni propriétaire ni ADMIN
     */

     public static function checkPlaylistOwner(int $playlistId): void
     {
         $user = AuthnProvider::getSignedInUser();
         $repo = DeefyRepository::getInstance();
         $stmt = $repo->getPdo()->prepare('SELECT * FROM user2playlist WHERE id_user = :userId AND id_pl = :playlistId');
         $stmt->execute(['userId' => $user->id, 'playlistId' => $playlistId]);
         $access = $stmt->fetch();
 
         if (!$access && $user->role !== 100) {
             throw new AuthnException("Accès refusé : rôle insuffisant.");
         }
     }
}