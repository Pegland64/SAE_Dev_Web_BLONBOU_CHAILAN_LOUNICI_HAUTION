<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

// nrv\net\repository\NrvRepository::setConfig('config.db.ini');

$dispatcher = new nrv\net\dispatch\Dispatcher();
$dispatcher->run();