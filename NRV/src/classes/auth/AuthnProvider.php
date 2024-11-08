<?php
namespace nrv\net\auth;

use nrv\net\exception\InvalidPropertyNameException;
use nrv\net\repository\NrvRepository;

class AuthnProvider
{
    public static function login(string $username, string $password) : void{
        $user = NrvRepository::getUserByUsername($username);
        if($user->password == $password){
            $_SESSION['user'] = $user;
        }
    }
}