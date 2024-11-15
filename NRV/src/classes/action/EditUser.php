<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\auth\Authz;

class EditUser extends Action{
    public function execute():string{
        // Récupère l'instance du repository
        $repo=NrvRepository::getInstance();

        // Récupère l'utilisateur connecté depuis la session
        $user=unserialize($_SESSION['user']);

        // Crée une instance d'Authz pour vérifier les permissions
        $authz=new Authz($repo->getUserbyUsername($user->username));

        // Vérifie si l'utilisateur a les droits nécessaires
        if(!$authz->checkStaffAdmin()){
            return "<p>Vous n'avez pas les droits pour accéder à cette page.</p>";
        }

        // Si la méthode HTTP est GET et que l'ID de l'utilisateur est défini
        if($this->http_method === 'GET' && isset($_GET['id'])){
            $id=$_GET['id'];

            // Récupère l'utilisateur par ID
            $user=$repo->getUserById($id);

            // Génère le formulaire HTML pour éditer l'utilisateur
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
            // Si la méthode HTTP est POST, met à jour le rôle de l'utilisateur
            $id=$_POST['id'];
            $role=$_POST['role'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $repo->updateRoleUser($user,$role);
            return "<p>Utilisateur mis à jour</p>";
        }else{
            return "<p>Erreur</p>";
        }
    }
}