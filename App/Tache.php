<?php

require_once __DIR__ . '/Entite.php';

class Tache extends Entite
{
    private $utilisateurId;
    private $categorieId;
    private $categorieNom;
    private $titre;
    private $description;
    private $statut;
    private $priorite;
    private $dateEcheance;
    private $dureeEstimee;

    private $nombreSousTaches;
    private $nombreSousTachesTerminees;

    public function getUtilisateurId()
    {
        return $this->utilisateurId;
    }

    public function getCategorieId()
    {
        return $this->categorieId;
    }

    public function getCategorieNom()
    {
        return $this->categorieNom;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getPriorite()
    {
        return $this->priorite;
    }

    public function getDateEcheance()
    {
        return $this->dateEcheance;
    }

    public function getDureeEstimee()
    {
        return $this->dureeEstimee;
    }

    public function getNombreSousTaches()
    {
        return $this->nombreSousTaches;
    }

    public function getNombreSousTachesTerminees()
    {
        return $this->nombreSousTachesTerminees;
    }

    public function getProgression()
    {
        if ($this->nombreSousTaches === null || $this->nombreSousTaches === 0) {
            return 0;
        }

        return (int) round(
            ($this->nombreSousTachesTerminees / $this->nombreSousTaches) * 100
        );
    }

    public function setUtilisateurId($utilisateurId)
    {
        $this->utilisateurId = $utilisateurId;
    }

    public function setCategorieId($categorieId)
    {
        $this->categorieId = $categorieId;
    }

    public function setCategorieNom($categorieNom)
    {
        $this->categorieNom = $categorieNom;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;
    }

    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;
    }

    public function setDureeEstimee($dureeEstimee)
    {
        $this->dureeEstimee = $dureeEstimee;
    }

    public function setNombreSousTaches($nombreSousTaches)
    {
        $this->nombreSousTaches = (int) $nombreSousTaches;
    }

    public function setNombreSousTachesTerminees($nombreSousTachesTerminees)
    {
        $this->nombreSousTachesTerminees = (int) $nombreSousTachesTerminees;
    }
}