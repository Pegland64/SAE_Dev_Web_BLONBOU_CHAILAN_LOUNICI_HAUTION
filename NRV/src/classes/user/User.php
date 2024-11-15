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

    const AVG_USER = 1;
    const STAFF_USER = 2;
    const ADMIN_USER = 3;

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
        if(!in_array($role, [self::ADMIN_USER, self::STAFF_USER, self::AVG_USER])) {
            throw new \Exception("Rôle invalide");
        }
    }
}