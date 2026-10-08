<?php

abstract class Entite
{
    protected $id;
    protected $dateCreation;
    protected $dateModification;

    public function getId()
    {
        return $this->id;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function getDateModification()
    {
        return $this->dateModification;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;
    }
}