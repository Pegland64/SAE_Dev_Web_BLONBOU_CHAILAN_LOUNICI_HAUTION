<?php

namespace nrv\net\action;

class EditUser extends Action{
    public function execute():string{
        if($this->http_method === 'GET' && isset($_GET['id'])){
            $id=$_GET['id'];
            $repo=NrvRepository::getInstance();
            $user=$repo->getUserById($id);
            $html = <<<HTML
            <form action="?action=edit-user" method="post" id="editUserForm">
                <input type="hidden" name="id" value="$user->id">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" value="$user->username" required>
                <br>
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" value="$user->email" required>
                <br>
                <label for="role">Role :</label>
                <select name="role" id="role">
                    <option value="admin" {$user->role === 'admin' ? 'selected' : ''}>Admin</option>
                    <option value="user" {$user->role === 'user' ? 'selected' : ''}>User</option>
                </select>
                <br>
                <button type="submit">Editer</button>
            </form>
            HTML;
            return $html;
        }
    }
}