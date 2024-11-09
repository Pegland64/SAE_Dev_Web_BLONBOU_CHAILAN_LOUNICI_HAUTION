<?php
declare(strict_types=1);

use nrv\net\render\Renderer;
use nrv\net\render\SoireeRenderer;
use nrv\net\show\Soiree;
use nrv\net\show\Spectacle;

require_once 'vendor/autoload.php';
session_start();

//iutnc\deefy\repository\DeefyRepository::setConfig('config.db.ini');
//
//$d = new \iutnc\deefy\dispatch\Dispatcher();
//$d->run();

// Créer des spectacles
$spectacle1 = new Spectacle('Spectacle 1', '2023-10-01', '20:00', 'image1.png', 'Artiste 1', 'Description 1', 'Style 1', 'Video 1');
$spectacle2 = new Spectacle('Spectacle 2', 'Artiste 2', '21:30', 'image2.png', 'Artiste 2', 'Description 2', 'Style 2', 'Video 2');

// Créer une soirée et ajouter des spectacles
$soiree = new Soiree('Soirée Thématique', 'Thématique 1', '2023-10-01', '19:00', 'Lieu 1', 50);
$soiree->addSpectacle($spectacle1);
$soiree->addSpectacle($spectacle2);

// Afficher la soirée
$renderer = new SoireeRenderer($soiree);
echo $renderer->render(Renderer::COMPACT);


