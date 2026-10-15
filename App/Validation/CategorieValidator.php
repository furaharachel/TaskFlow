<?php

require_once __DIR__ . '/../Entity/Categorie.php';
require_once __DIR__ . '/Validator.php';

class CategorieValidator
{
    private $erreurs = array();

    public function valider(Categorie $categorie)
    {
        $this->erreurs = array();

        $nom = trim((string) $categorie->getNom());

        if (!Validator::requis($nom)) {
            $this->erreurs['nom'] =
                'Le nom de la catégorie est obligatoire.';
        } elseif (!Validator::longueurMaximale($nom, 100)) {
            $this->erreurs['nom'] =
                'Le nom ne peut pas dépasser 100 caractères.';
        }

        return empty($this->erreurs);
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }
}