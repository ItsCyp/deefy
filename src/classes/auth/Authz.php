<?php

namespace iutnc\deefy\auth;


use iutnc\deefy\auth\User;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AccessControlException;

/**
 * Classe Authz
 * Gère les autorisations des utilisateurs.
 */
class Authz
{
    private User $authenticated_user;

    /**
     * Constructeur de la classe Authz.
     *
     * @param User $user L'utilisateur authentifié.
     */
    public function __construct(User $user)
    {
        $this->authenticated_user = $user;
    }

    /**
     * Vérifie si l'utilisateur a le rôle requis.
     *
     * @param int $required Le rôle requis.
     * @throws AccessControlException Si l'utilisateur n'a pas le rôle requis.
     */
    public function checkRole(int $required): void
    {
        $user = AuthnProvider::getSignedInUser();
        if ($user->role >= $required) {
            throw new AccessControlException("Vous n'avez pas le rôle requis pour accéder à cette ressource.");
        }
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de la playlist.
     *
     * @param int $playlistId L'identifiant de la playlist.
     * @throws AccessControlException Si l'utilisateur n'a pas accès à la playlist ou n'est pas administrateur.
     */
    public static function checkPlaylistOwner(int $playlistId): void
    {
        try {
            $user = AuthnProvider::getSignedInUser();
            $repo = DeefyRepository::getInstance();
            $stmt = $repo->getPdo()->prepare('SELECT * FROM user2playlist WHERE id_user = :userId AND id_pl = :playlistId');
            $stmt->execute(['userId' => $user->id, 'playlistId' => $playlistId]);
            $access = $stmt->fetch();

            if (!$access && $user->role !== 100) {
                throw new AccessControlException("L'utilisateur n'a pas accès à la playlist ou n'est pas administrateur.");
            }
        } catch (\PDOException $e) {
            throw new AccessControlException("Erreur lors de la vérification de l'accès à la playlist : " . $e->getMessage());
        }
    }
}