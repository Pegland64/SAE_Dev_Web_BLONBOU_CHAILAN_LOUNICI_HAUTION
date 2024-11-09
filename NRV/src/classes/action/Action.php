<?php

namespace nrv\net\action;

abstract class Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;


    public function __construct(){

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    public function execute() : string
    {
        $html = "";
        if($this->http_method === 'GET')
        {
            $html = $this->executeGET();
        }else if($this->http_method === 'POST')
        {
            $html = $this->executePOST();
        }
        return $html;
    }

    abstract public function executeGET() : string;
    abstract public function executePOST() : string;



}