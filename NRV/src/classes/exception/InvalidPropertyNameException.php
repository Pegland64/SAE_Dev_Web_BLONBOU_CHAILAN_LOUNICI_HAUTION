<?php

namespace nrv\net\exception;

use Exception;

class InvalidPropertyNameException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Invalid property name: $property");
    }

}