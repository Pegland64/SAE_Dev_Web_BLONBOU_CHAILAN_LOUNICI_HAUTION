<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\action\Action;
use nrv\net\auth\Authz;

class EditUser extends Action{
    public function execute():string{
        $repo=NrvRepository::getInstance();
        $user=unserialize($_SESSION['user']);
        $authz=new Authz($repo->getUserbyUsername($user->username));
        if(!$authz->checkStaffAdmin()){
            return "<p>Vous n'avez pas les droits pour accéder à cette page.</p>";
        }
        if($this->http_method === 'GET' && isset($_GET['id'])){
            $id=$_GET['id'];
            
            $user=$repo->getUserById($id);
            $html = <<<HTML
                <form action="?action=edit-user" method="post" id="editUserForm">
                    <input type="hidden" name="id" value="$user->id">
                    <h1>Utilisateur :</h1>
                    <h2>{$user->username}</h2>
                    <br> 
                    <label for="role">Role :</label>
                    <select name="role" id="role">
                        <option value="1" selected>User</option>
                        <option value="2">Staff</option>
                        <option value="3">Admin</option>
                    </select>       
                    <br>
                    <button type="submit">Editer</button>
                </form>
                HTML;
                return $html;
        }elseif($this->http_method === 'POST'){
            $id=$_POST['id'];
            $role=$_POST['role'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $repo->updateRoleUser($user,$role);
            return "<p>Utilisateur mis à jour</p>";
        }
    }
}