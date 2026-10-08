<?php

require_once __DIR__ . '/Entite.php';

class Rappel extends Entite
{
    private $tacheId;
    private $dateRappel;
    private $message;
    private $statut;

    public function getTacheId()
    {
        return $this->tacheId;
    }

    public function getDateRappel()
    {
        return $this->dateRappel;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setTacheId($tacheId)
    {
        $this->tacheId = $tacheId;
    }

    public function setDateRappel($dateRappel)
    {
        $this->dateRappel = $dateRappel;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
}