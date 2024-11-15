<?php

namespace nrv\net\action;

use nrv\net\repository\NrvRepository;
use nrv\net\show\Lieu;
use nrv\net\show\Soiree;

class AddSoireeAction extends Action
{

    public function execute(): string
    {

        if(!isset($_SESSION['user']) || (unserialize($_SESSION['user'])->role !== 2 && unserialize($_SESSION['user'])->role !== 3)){
            return "<div>Accès refusé. Vous devez être un membre du staff ou un administrateur pour accéder à cette fonctionnalité.</div>";
        }



        $html = "";

        if ($this->http_method === 'GET') {
            $html = $this->renderForm();
        } else if ($this->http_method === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $thematique = $_POST['thematique'] ?? '';
            $date = new \DateTime($_POST['date'] ?? '');
            $horaire = new \DateTime($_POST['horaire'] ?? '');
            $duree = new \DateTime($_POST['duree'] ?? '');
            $lieu = new Lieu($_POST['lieu'] ?? '', '', 0, 0, '');
            $tarif = (float)$_POST['tarif'] ?? 0.0;

            $soiree = new Soiree($nom, $thematique, $date, $horaire, $lieu, $tarif);
            $soiree->setDuree($duree);

            NrvRepository::getInstance()->addSoiree($soiree);

            $html = "<p>Soirée ajoutée avec succès.</p>";
        }
        return $html;
    }

    private function renderForm(): string
    {
        $lieux = NrvRepository::getInstance()->getAllLieux();
        $lieuOptions = "";
        foreach ($lieux as $lieu) {
            $lieuOptions .= "<option value=\"{$lieu->nom}\">{$lieu->nom}</option>";
        }

        return <<<HTML
        <form method="POST" action="?action=add-soiree">
            <label for="nom">Nom de la soirée:</label>
            <input type="text" id="nom" name="nom" required><br>

            <label for="thematique">Thématique:</label>
            <input type="text" id="thematique" name="thematique" required><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br>

            <label for="horaire">Horaire :</label>
            <input type="time" id="horaire" name="horaire" required><br>

            <label for="duree">Durée :</label>
            <input type="text" id="duree" name="duree" placeholder="HH:MM:SS" required><br>

            <label for="lieu">Lieu:</label>
            <select id="lieu" name="lieu" required>
                $lieuOptions
            </select><br>

            <label for="tarif">Tarif:</label>
            <input type="number" id="tarif" name="tarif" required><br>

            <input type="submit" value="Ajouter la soirée">
        </form>
        HTML;
    }
}