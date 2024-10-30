<?php

namespace iutnc\deefy\auth;


use iutnc\deefy\auth\User;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AccessControlException;



class Authz
{
    private User $authenticated_user;

    public function __construct(User $user)
    {
        $this->authenticated_user = $user;
    }

    public function checkRole(int $required): void
    {
        $user = AuthnProvider::getSignedInUser();
        if ($user->role >= $required) {
            throw new AccessControlException("You do not have the required role to access this resource.");
        }
    }

    public static function checkPlaylistOwner(int $playlistId): void
    {
        $user = AuthnProvider::getSignedInUser();
        $repo = DeefyRepository::getInstance();
        $stmt = $repo->getPdo()->prepare('SELECT * FROM user2playlist WHERE id_user = :userId AND id_pl = :playlistId');
        $stmt->execute(['userId' => $user->id, 'playlistId' => $playlistId]);
        $access = $stmt->fetch();

        if (!$access && $user->role !== 100) {
            throw new AccessControlException("User does not have access to the playlist or is not an admin.");
        }
    }
}