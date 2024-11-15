<?php

namespace nrv\net\auth;

use nrv\net\user\User;
use nrv\net\exception\AccessControlException;

class Authz
{
    private User $authenticated_user;

    public function __construct(User $user)
    {
        $this->authenticated_user = $user;
    }

    /**
     * Vérifie si l'utilisateur a le rôle requis.
     * @param int $required Le rôle requis.
     * @throws \Exception Si l'utilisateur n'a pas le rôle requis.
     */
    public function checkRole(int $required) : void
    {
        $user = AuthnProvider::getSignedInUser();
        if($user->role >= $required){
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette ressource.";
            //throw new \AccessControlException("Vous n'avez pas les droits nécessaires pour accéder à cette ressource.");
        }
    }

    /**
     * Vérifier si l'utilisateur a les droit staff et admin.
     * @throws \Exception Si l'utilisateur n'a pas les droits requis.
     */
    public function checkStaffAdmin() : void
    {
        if($this->authenticated_user->role < User::STAFF_USER){
            echo "Vous n'avez pas les droits nécessaires pour accéder à cette ressource.";
        }
    }

}