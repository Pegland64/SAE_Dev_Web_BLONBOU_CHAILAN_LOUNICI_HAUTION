<?php
namespace nrv\net\user;

use nrv\net\exception\InvalidPropertyNameException;


class User
{
    private int $id;
    private String $username;
    private String $password;
    private String $email;
    private int $role;

    private static int $AVG_USER = 1;
    private static int $STAFF_USER = 10;
    private static int $ADMIN_USER = 100;

    public function __construct(int $id, String $username, String $password, String $email, int $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new InvalidPropertyNameException($property);
        }
    }

    public function setRole(int $role)
    {
        if(!in_array($role, [User::$ADMIN_USER, User::$STAFF_USER, User::$AVG_USER])) {
            throw new \Exception("Rôle invalide");
        }
    }
}