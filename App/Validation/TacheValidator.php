<?php

require_once __DIR__ . '/../Entity/Tache.php';
require_once __DIR__ . '/Validator.php';

class TacheValidator
{
    private $erreurs = array();

    public function valider(Tache $tache)
    {
        $this->erreurs = array();

        $titre = trim((string) $tache->getTitre());

        if (!Validator::requis($titre)) {
            $this->erreurs['titre'] =
                'Le titre de la tâche est obligatoire.';
        } elseif (!Validator::longueurMaximale($titre, 150)) {
            $this->erreurs['titre'] =
                'Le titre ne peut pas dépasser 150 caractères.';
        }

        $statutsAutorises = array(
            'a_faire',
            'en_cours',
            'terminee'
        );

        if (!Validator::valeurAutorisee(
            $tache->getStatut(),
            $statutsAutorises
        )) {
            $this->erreurs['statut'] =
                'Le statut sélectionné est invalide.';
        }

        $prioritesAutorisees = array(
            'basse',
            'normale',
            'haute',
            'urgente'
        );

        if (!Validator::valeurAutorisee(
            $tache->getPriorite(),
            $prioritesAutorisees
        )) {
            $this->erreurs['priorite'] =
                'La priorité sélectionnée est invalide.';
        }

        $duree = $tache->getDureeEstimee();

        if ($duree !== null && $duree !== '') {
            if (!Validator::entierPositif($duree)) {
                $this->erreurs['duree_estimee'] =
                    'La durée doit être un nombre entier supérieur à 0.';
            }
        }

        return empty($this->erreurs);
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }
}