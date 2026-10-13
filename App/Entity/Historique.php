<?php

require_once __DIR__ . '/Entite.php';

class Historique extends Entite
{
    private $utilisateurId;
    private $tacheId;
    private $tacheTitre;
    private $action;
    private $description;
    private $dateAction;

    public function getUtilisateurId()
    {
        return $this->utilisateurId;
    }

    public function getTacheId()
    {
        return $this->tacheId;
    }

    public function getTacheTitre()
    {
        return $this->tacheTitre;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDateAction()
    {
        return $this->dateAction;
    }

    public function setUtilisateurId($utilisateurId)
    {
        $this->utilisateurId = $utilisateurId;
    }

    public function setTacheId($tacheId)
    {
        $this->tacheId = $tacheId;
    }

    public function setTacheTitre($tacheTitre)
    {
        $this->tacheTitre = $tacheTitre;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;
    }
}