<?php
declare(strict_types=1);

use nrv\net\render\Renderer;
use nrv\net\render\SoireeRenderer;
use nrv\net\show\Soiree;
use nrv\net\show\Spectacle;

require_once 'vendor/autoload.php';
session_start();

\nrv\net\repository\NrvRepository::setConfig('config.db.ini');

$d = new \nrv\net\dispatch\Dispatcher();
$d->run();


