<?php

require_once __DIR__ . '/../Entity/SousTache.php';

/**
 * Valide les données d'une sous-tâche.
 */
class SousTacheValidator
{
    private $erreurs = array();

    public function valider(SousTache $sousTache)
    {
        $this->erreurs = array();

        $titre = trim($sousTache->getTitre());

        if ($titre === '') {
            $this->erreurs['titre'] =
                'Le titre de la sous-tâche est obligatoire.';
        } elseif (strlen($titre) > 150) {
            $this->erreurs['titre'] =
                'Le titre ne peut pas dépasser 150 caractères.';
        }

        $statutsAutorises = array(
            'a_faire',
            'terminee'
        );

        if (!in_array(
            $sousTache->getStatut(),
            $statutsAutorises,
            true
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