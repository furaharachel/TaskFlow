<?php

/**
 * Represente le resultat d'une validation.
 * Compatible avec PHP 5.5.12.
 */
class ValidationResultat
{
    private $erreurs;

    public function __construct()
    {
        $this->erreurs = array();
    }

    public function ajouterErreur($message)
    {
        $this->erreurs[] = $message;
    }

    public function estValide()
    {
        return count($this->erreurs) === 0;
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }

    public function aDesErreurs()
    {
        return count($this->erreurs) > 0;
    }
}