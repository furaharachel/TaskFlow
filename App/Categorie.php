<?php

require_once __DIR__ . '/Entite.php';

class Categorie extends Entite
{
    private $nom;
    private $description;

    public function getNom()
    {
        return $this->nom;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}