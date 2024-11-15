<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\user\User;

class AddSpectacleAction extends Action
{
    public function execute(): string
    {
        // Vérification des permissions de l'utilisateur
        if (!isset($_SESSION['user']) || (unserialize($_SESSION['user'])->role !== 2 && unserialize($_SESSION['user'])->role !== 3)) {
            return "<div>Accès refusé. Vous devez être un membre du staff ou un administrateur pour accéder à cette fonctionnalité.</div>";
        }

        // Affichage du formulaire (méthode GET)
        if ($this->http_method === 'GET') {
            return $this->renderForm();
        }

        // Traitement du formulaire (méthode POST)
        if ($this->http_method === 'POST') {
            return $this->handleFormSubmission();
        }

        return "<div>Action non valide.</div>";
    }

    private function renderForm(): string
    {
        return <<<HTML
        <form action="?action=creer_spectacle" method="post">
            <label for="titre">Titre :</label>
            <input type="text" name="titre" id="titre" required>
            <br>
            
            <label for="description">Description :</label>
            <textarea name="description" id="description" required></textarea>
            <br>
            
            <label for="horaire_debut">Horaire de début :</label>
            <input type="time" name="horaire_debut" id="horaire_debut" required>
            <br>
            
            <label for="horaire_fin">Horaire de fin :</label>
            <input type="time" name="horaire_fin" id="horaire_fin" required>
            <br>
            
            <label for="style">Style :</label>
            <input type="text" name="style" id="style" required>
            <br>
            
            <label for="etat">État :</label>
            <select name="etat" id="etat" required>
                <option value="confirmé">Confirmé</option>
                <option value="annulé">Annulé</option>
            </select>
            <br>
            
            <label for="id_soiree">Soirée associée :</label>
            <input type="number" name="id_soiree" id="id_soiree" required>
            <br>
            
            <button type="submit">Créer le spectacle</button>
        </form>
HTML;
    }

    private function handleFormSubmission(): string
    {
        // Récupération des données POST
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $horaire_debut = $_POST['horaire_debut'] ?? '';
        $horaire_fin = $_POST['horaire_fin'] ?? '';
        $style = trim($_POST['style'] ?? '');
        $etat = $_POST['etat'] ?? '';
        $id_soiree = $_POST['id_soiree'] ?? '';

        // Validation des champs
        if (empty($titre) || empty($description) || empty($horaire_debut) || empty($horaire_fin) || empty($style) || empty($etat) || empty($id_soiree)) {
            return "<div>Erreur : Tous les champs sont obligatoires.</div>";
        }

        if (!in_array($etat, ['confirmé', 'annulé'], true)) {
            return "<div>Erreur : L'état choisi est invalide.</div>";
        }

        if (!preg_match('/^\d+$/', $id_soiree)) {
            return "<div>Erreur : L'ID de la soirée doit être un nombre valide.</div>";
        }

        // Appel au repository pour créer le spectacle
        $repo = NrvRepository::getInstance();
        try {
            $repo->createSpectacle($titre, $description, $horaire_debut, $horaire_fin, $style, $etat, (int)$id_soiree);
            return "<div>Le spectacle a été créé avec succès.</div>";
        } catch (\Exception $e) {
            return "<div>Erreur lors de la création du spectacle : {$e->getMessage()}</div>";
        }
    }
}