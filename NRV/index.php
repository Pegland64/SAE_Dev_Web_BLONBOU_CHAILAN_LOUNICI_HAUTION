<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php';

use nrv\net\dispatch\Dispatcher;
use nrv\net\repository\NrvRepository;

// Initialise la configuration de la base de données
NrvRepository::setConfig('config.db.ini');

// Instancie le Dispatcher pour gérer les actions
$dispatcher = new Dispatcher();
$dispatcher->run(); // Exécute le Dispatcher pour générer et afficher le contenu
