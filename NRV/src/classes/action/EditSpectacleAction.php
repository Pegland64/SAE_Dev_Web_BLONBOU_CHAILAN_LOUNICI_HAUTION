<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\show\Spectacle;

class EditSpectacleAction extends Action
{
    public function execute(): string
    {
        // Vérification des permissions de l'utilisateur
        if (!isset($_SESSION['user']) || (unserialize($_SESSION['user'])->role !== 2 && unserialize($_SESSION['user'])->role !== 3)) {
            return "<div>Accès refusé. Vous devez être un membre du staff ou un administrateur pour accéder à cette fonctionnalité.</div>";
        }

        // Vérifie si la méthode HTTP est GET
        if ($this->http_method === 'GET') {
            if (!isset($_GET['id_spectacle'])) {
                return "<div>Spectacle inconnu.</div>";
            }

            // Récupère le spectacle par ID
            $spectacle = NrvRepository::getInstance()->getSpectacleById((int)$_GET['id_spectacle']);
            return $this->renderForm($spectacle);
        } elseif ($this->http_method === 'POST') {
            if (!isset($_POST['id_spectacle'])) {
                return "<div>Spectacle inconnu.</div>";
            }

            // Récupère le spectacle par ID
            $id_spectacle = (int)$_POST['id_spectacle'];
            $spectacle = NrvRepository::getInstance()->getSpectacleById($id_spectacle);

            // Récupération des données POST
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $horaire_debut = new \DateTime($_POST['horaire_debut']);
            $horaire_fin = new \DateTime($_POST['horaire_fin']);
            $style = $_POST['style'];
            $etat = $_POST['etat'];
            $id_soiree = (int)$_POST['id_soiree'];

            // Met à jour les champs via setters les champs via setters
            $spectacle->setTitre($titre);
            $spectacle->setDescription($description);
            $spectacle->setHoraire($horaire_debut);
            $spectacle->setDuree((new \DateTime('00:00:00'))->add($horaire_debut->diff($horaire_fin)));
            $spectacle->setStyle($style);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);

            // Mise à jour dans la base de données
            NrvRepository::getInstance()->updateSpectacle($spectacle);

            return "<div>Le spectacle a été mis à jour avec succès.</div>";
        }

        return "<div>Action non valide.</div>";
    }

    private function renderForm(Spectacle $spectacle): string
    {
        $soirees = NrvRepository::getInstance()->getAllSoirees();

        $soireeOptions = '';
        foreach ($soirees as $soiree) {
            $selected = $spectacle->id_soiree === $soiree->id_soiree ? 'selected' : '';
            $soireeOptions .= "<option value='{$soiree->id_soiree}' $selected>{$soiree->nom}</option>";
        }

        return <<<HTML
    <form action="?action=edit-spectacle" method="post">
        <input type="hidden" name="id_spectacle" value="{$spectacle->id_spectacle}">
        
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre" value="{$spectacle->titre}" required>
        <br>

        <label for="description">Description :</label>
        <textarea name="description" id="description" required>{$spectacle->description}</textarea>
        <br>

        <label for="horaire_debut">Horaire de début :</label>
        <input type="time" name="horaire_debut" id="horaire_debut" value="{$spectacle->horaire->format('H:i')}" required>
        <br>

        <label for="horaire_fin">Horaire de fin :</label>
        <input type="time" name="horaire_fin" id="horaire_fin" value="{$spectacle->horaire->add($spectacle->duree->diff(new \DateTime('00:00:00')))->format('H:i')}" required>
        <br>

        <label for="style">Style :</label>
        <input type="text" name="style" id="style" value="{$spectacle->style}" required>
        <br>

        <label for="etat">État :</label>
        <select name="etat" id="etat" required>
            <option value="confirmé" {$this->getSelected($spectacle->etat, 'confirmé')}>Confirmé</option>
            <option value="annulé" {$this->getSelected($spectacle->etat, 'annulé')}>Annulé</option>
        </select>
        <br>

        <label for="id_soiree">Soirée associée :</label>
        <select name="id_soiree" id="id_soiree" required>
            $soireeOptions
        </select>
        <br>

        <button type="submit">Mettre à jour le spectacle</button>
    </form>
HTML;
    }

    private function getSelected(string $current, string $value): string
    {
        return $current === $value ? 'selected' : '';
    }
}
