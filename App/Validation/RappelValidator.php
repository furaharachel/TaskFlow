<?php

require_once __DIR__ . '/../Entity/Rappel.php';

/**
 * Valide les données d'un rappel.
 */
class RappelValidator
{
    private $erreurs = array();

    public function valider(Rappel $rappel)
    {
        $this->erreurs = array();

        // Validation de la date du rappel
        $dateRappel = trim($rappel->getDateRappel());
        $message = trim($rappel->getMessage());

        if ($dateRappel === '') {
            $this->erreurs['date_rappel'] =
                'La date du rappel est obligatoire.';
        } else {
            $date = DateTime::createFromFormat(
                'Y-m-d H:i',
                $dateRappel
            );

            if (
                $date === false
                || $date->format('Y-m-d H:i') !== $dateRappel
            ) {
                $this->erreurs['date_rappel'] =
                    'La date du rappel est invalide.';
            }
        }

        // Validation du message
        if ($message === '') {
            $this->erreurs['message'] =
                'Le message du rappel est obligatoire.';
        } elseif (strlen($message) > 255) {
            $this->erreurs['message'] =
                'Le message ne peut pas dépasser 255 caractères.';
        }

        // Validation du statut
        $statutsAutorises = array(
            'actif',
            'termine'
        );

        if (!in_array(
            $rappel->getStatut(),
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