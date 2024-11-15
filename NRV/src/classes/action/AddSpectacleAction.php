<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\show\Spectacle;
use nrv\net\user\User;

class AddSpectacleAction extends Action
{
    public function execute(): string
    {
        // Vérification des permissions de l'utilisateur
        if (!isset($_SESSION['user']) || (unserialize($_SESSION['user'])->role !== 2 && unserialize($_SESSION['user'])->role !== 3)) {
            return "<div>Accès refusé. Vous devez être un membre du staff ou un administrateur pour accéder à cette fonctionnalité.</div>";
        }

        if($this->http_method === 'GET') {
            return $this->renderForm();
        } else if($this->http_method === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $horaire_debut = new \DateTime($_POST['horaire_debut']);
            $horaire_fin = new \DateTime($_POST['horaire_fin']);

            if ($horaire_debut > $horaire_fin) {
                $horaire_fin->add(new \DateInterval('P1D'));
            }

            $interval = $horaire_debut->diff($horaire_fin);
            $duree = new \DateTime($interval->format('%H:%I:%S'));

            $style = $_POST['style'];
            $etat = $_POST['etat'];
            $id_soiree = (int)$_POST['id_soiree'];
            $artistes = $_POST['artistes'];
            $images = $_FILES['images'];

            $spectacle = new Spectacle($titre, $description, '', $horaire_debut, $duree, $style);
            $spectacle->setEtat($etat);
            $spectacle->setIdSoiree($id_soiree);

            NrvRepository::getInstance()->addSpectacle($spectacle, $artistes, $images);

            return "<div>Le spectacle a été créé avec succès.</div>";
        }

        return "<div>Action non valide.</div>";
    }

    private function renderForm(): string
    {
        $soirees = NrvRepository::getInstance()->getAllSoirees();
        $artistes = NrvRepository::getInstance()->getAllArtistes();

        $soireeOptions = '';
        foreach ($soirees as $soiree) {
            $soireeOptions .= "<option value='{$soiree->id_soiree}'>{$soiree->nom}</option>";
        }

        $artisteOptions = '';
        foreach ($artistes as $artiste) {
            $artisteOptions .= "<option value='{$artiste->id_artiste}'>{$artiste->nom_artiste}</option>";
        }

        return <<<HTML
    <form action="?action=add-spectacle" method="post" enctype="multipart/form-data">
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
        <select name="id_soiree" id="id_soiree" required>
            $soireeOptions
        </select>
        <br>

        <label for="artistes">Artistes :</label>
        <select name="artistes[]" id="artistes" multiple required>
            $artisteOptions
        </select>
        <br>

        <label for="images">Images :</label>
        <input type="file" name="images[]" id="images" multiple>
        <br>

        <button type="submit">Créer le spectacle</button>
    </form>
HTML;
    }
}