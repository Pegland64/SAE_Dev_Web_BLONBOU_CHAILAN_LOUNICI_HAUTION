<?php

namespace nrv\net\action;

use nrv\net\auth\Authz;
use nrv\net\repository\NrvRepository;

class ListesUser extends Action
{
    public function execute() : string
    {
        $repo=NrvRepository::getInstance();
        if(isset($_SESSION['user'])){
            $user=unserialize($_SESSION['user']);
            $authz=new Authz($user);
            if(!$authz->checkStaffAdmin()){
                return "<p>Vous n'avez pas les droits pour accéder à cette page.</p>";
            }
            $users=$repo->getAllUsers();
            $html="<h1>Liste des utilisateurs</h1>";
            $html.="<table>";
            $html.="<tr><th>Id</th><th>Nom d'utilisateur</th><th>Email</th><th>Role</th><th>Actions</tr>";
            foreach($users as $user){
                $html.="<tr><td>".$user->id."</td><td>".$user->username."</td><td>".$user->email."</td><td>".$user->role."</td>";
                $html.="<td><a href='?action=edit-user&id=".$user->id."'>Editer</a> | <a href='?action=delete-user&id=".$user->id."'>Supprimer</a></td></tr>";
            }
            $html.="</table>";
            return $html;  
        }else{
            return "<p>Vous devez être connecté pour accéder à cette page.</p>";
        }
    }
}