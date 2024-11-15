<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\auth\Authz;

class SupprimerUser extends Action{
    public function execute():string{
        // Récupère l'instance du repository
        $repo=NrvRepository::getInstance();
        // Désérialise l'utilisateur de la session
        $user=unserialize($_SESSION['user']);
        // Crée une instance d'authentification pour l'utilisateur
        $authz=new Authz($repo->getUserbyUsername($user->username));
        // Vérifie si l'utilisateur a les droits d'accès
        if(!$authz->checkStaffAdmin()){
            return "<p>Vous n'avez pas les droits pour accéder à cette page.</p>";
        }
        // Gère la requête GET pour afficher le formulaire de suppression
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
        // Gère la requête POST pour traiter la suppression
        }elseif($this->http_method === 'POST'){
            $id=$_POST['id'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $repo->deleteUser($user);
            return "<p>Utilisateur supprimé</p>";
        }else{
            return "<p>Erreur</p>";
        }
    }
}