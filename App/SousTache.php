<?php

require_once __DIR__ . '/Entite.php';

class SousTache extends Entite
{
    private $tacheId;
    private $titre;
    private $statut;

    public function getTacheId()
    {
        return $this->tacheId;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setTacheId($tacheId)
    {
        $this->tacheId = $tacheId;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
}