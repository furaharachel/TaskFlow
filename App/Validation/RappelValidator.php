<?php

require_once __DIR__ . '/../Entity/Rappel.php';
require_once __DIR__ . '/Validator.php';

class RappelValidator
{
    private $erreurs = array();

    public function valider(Rappel $rappel)
    {
        $this->erreurs = array();

        $dateRappel = trim((string) $rappel->getDateRappel());
        $message = trim((string) $rappel->getMessage());

        if (!Validator::requis($dateRappel)) {
            $this->erreurs['date_rappel'] =
                'La date du rappel est obligatoire.';
        } elseif (!Validator::dateHeureValide($dateRappel)) {
            $this->erreurs['date_rappel'] =
                'La date du rappel est invalide.';
        }

        if (!Validator::requis($message)) {
            $this->erreurs['message'] =
                'Le message du rappel est obligatoire.';
        } elseif (!Validator::longueurMaximale($message, 255)) {
            $this->erreurs['message'] =
                'Le message ne peut pas dépasser 255 caractères.';
        }

        $statutsAutorises = array(
            'actif',
            'termine'
        );

        if (!Validator::valeurAutorisee(
            $rappel->getStatut(),
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