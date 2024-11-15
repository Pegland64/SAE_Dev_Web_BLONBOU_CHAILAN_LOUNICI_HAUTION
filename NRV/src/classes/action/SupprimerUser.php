<?php

namespace nrv\net\action;
use nrv\net\repository\NrvRepository;
use nrv\net\action\Action;
use nrv\net\auth\Authz;

class SupprimerUser extends Action{
    public function execute():string{
        $repo=NrvRepository::getInstance();
        $user=unserialize($_SESSION['user']);
        $authz=new Authz($repo->getUserbyUsername($user->username));
        if(!$authz->checkStaffAdmin()){
            return "<p>Vous n'avez pas les droits pour accéder à cette page.</p>";
        }
        if($this->http_method === 'GET' && isset($_GET['id'])){
            $id=$_GET['id'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $html = <<<HTML
                <form action="?action=delete-user" method="post" id="deleteUserForm">
                    <input type="hidden" name="id" value="$user->id">
                    <h1>Utilisateur :</h1>
                    <h2>{$user->username}</h2>
                    <br> 
                    <button type="submit">Supprimer</button>
                </form>
                HTML;
                return $html;
        }elseif($this->http_method === 'POST'){
            $id=$_POST['id'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $repo->deleteUser($user);
            return "<p>Utilisateur supprimé</p>";
        }
    }
}