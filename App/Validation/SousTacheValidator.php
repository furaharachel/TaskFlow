<?php

require_once __DIR__ . '/../Entity/SousTache.php';
require_once __DIR__ . '/Validator.php';

class SousTacheValidator
{
    private $erreurs = array();

    public function valider(SousTache $sousTache)
    {
        $this->erreurs = array();

        $titre = trim((string) $sousTache->getTitre());

        if (!Validator::requis($titre)) {
            $this->erreurs['titre'] =
                'Le titre de la sous-tâche est obligatoire.';
        } elseif (!Validator::longueurMaximale($titre, 150)) {
            $this->erreurs['titre'] =
                'Le titre ne peut pas dépasser 150 caractères.';
        }

        $statutsAutorises = array(
            'a_faire',
            'terminee'
        );

        if (!Validator::valeurAutorisee(
            $sousTache->getStatut(),
            $statutsAutorises
        )) {
            $this->erreurs['statut'] =
                'Le statut sélectionné est invalide.';
        }

        return empty($this->erreurs);
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }
}