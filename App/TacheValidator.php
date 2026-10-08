<?php

require_once __DIR__ . '/Tache.php';

class TacheValidator
{
    private $erreurs = array();

    public function valider(Tache $tache)
    {
        $this->erreurs = array();

        if (trim($tache->getTitre()) === '') {
            $this->erreurs['titre'] = 'Le titre de la tâche est obligatoire.';
        } elseif (strlen($tache->getTitre()) > 150) {
            $this->erreurs['titre'] = 'Le titre ne peut pas dépasser 150 caractères.';
        }

        if ($tache->getStatut() === '') {
            $this->erreurs['statut'] = 'Le statut est obligatoire.';
        }

        $statutsAutorises = array(
            'a_faire',
            'en_cours',
            'terminee'
        );

        if (
            $tache->getStatut() !== '' &&
            !in_array($tache->getStatut(), $statutsAutorises)
        ) {
            $this->erreurs['statut'] = 'Le statut sélectionné est invalide.';
        }

        if ($tache->getPriorite() === '') {
            $this->erreurs['priorite'] = 'La priorité est obligatoire.';
        }

        $prioritesAutorisees = array(
            'basse',
            'normale',
            'haute',
            'urgente'
        );

        if (
            $tache->getPriorite() !== '' &&
            !in_array($tache->getPriorite(), $prioritesAutorisees)
        ) {
            $this->erreurs['priorite'] = 'La priorité sélectionnée est invalide.';
        }

        if ($tache->getDureeEstimee() !== null && $tache->getDureeEstimee() !== '') {
            if (
                !is_numeric($tache->getDureeEstimee()) ||
                $tache->getDureeEstimee() < 1
            ) {
                $this->erreurs['duree_estimee'] =
                    'La durée estimée doit être un nombre supérieur à 0.';
            }
        }

        return empty($this->erreurs);
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }
}