<?php

require_once __DIR__ . '/Tache.php';

class TacheValidator
{
    private $erreurs = array();

    public function valider(Tache $tache)
    {
        $this->erreurs = array();

        /*
         * Validation du titre
         */
        $titre = trim($tache->getTitre());

        if ($titre === '') {

            $this->erreurs['titre'] =
                'Le titre de la tâche est obligatoire.';

        } elseif (strlen(utf8_decode($titre)) > 150) {

            $this->erreurs['titre'] =
                'Le titre ne peut pas dépasser 150 caractères.';
        }

        /*
         * Validation du statut
         */
        $statutsAutorises = array(
            'a_faire',
            'en_cours',
            'terminee'
        );

        if (!in_array($tache->getStatut(), $statutsAutorises, true)) {

            $this->erreurs['statut'] =
                'Le statut sélectionné est invalide.';
        }

        /*
         * Validation de la priorité
         */
        $prioritesAutorisees = array(
            'basse',
            'normale',
            'haute',
            'urgente'
        );

        if (!in_array($tache->getPriorite(), $prioritesAutorisees, true)) {

            $this->erreurs['priorite'] =
                'La priorité sélectionnée est invalide.';
        }

        /*
         * Validation de la durée estimée
         */
        $duree = $tache->getDureeEstimee();

        if ($duree !== null && $duree !== '') {

            if (
                !is_numeric($duree) ||
                (int) $duree < 1 ||
                (string) (int) $duree !== (string) $duree
            ) {

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