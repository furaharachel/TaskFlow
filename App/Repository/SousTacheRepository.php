<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/SousTache.php';

class SousTacheRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function ajouter(SousTache $sousTache)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'INSERT INTO sous_taches
            (
                tache_id,
                titre,
                statut,
                date_creation,
                date_modification
            )
            VALUES
            (
                :tache_id,
                :titre,
                :statut,
                :date_creation,
                :date_modification
            )'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':tache_id' => $sousTache->getTacheId(),
            ':titre' => $sousTache->getTitre(),
            ':statut' => $sousTache->getStatut(),
            ':date_creation' => $date,
            ':date_modification' => $date
        ));

        $sousTache->setId($connection->lastInsertId());
        $sousTache->setDateCreation($date);
        $sousTache->setDateModification($date);

        return $sousTache;
    }

    public function trouverParTache($tacheId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM sous_taches
             WHERE tache_id = :tache_id
             ORDER BY id ASC'
        );

        $requete->execute(array(
            ':tache_id' => $tacheId
        ));

        $sousTaches = array();

        while ($donnees = $requete->fetch()) {
            $sousTaches[] = $this->creerObjet($donnees);
        }

        return $sousTaches;
    }

    public function trouverParId($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM sous_taches
             WHERE id = :id'
        );

        $requete->execute(array(
            ':id' => $id
        ));

        $donnees = $requete->fetch();

        if (!$donnees) {
            return null;
        }

        return $this->creerObjet($donnees);
    }

    public function modifier(SousTache $sousTache)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'UPDATE sous_taches
             SET
                titre = :titre,
                statut = :statut,
                date_modification = :date_modification
             WHERE id = :id'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':titre' => $sousTache->getTitre(),
            ':statut' => $sousTache->getStatut(),
            ':date_modification' => $date,
            ':id' => $sousTache->getId()
        ));

        $sousTache->setDateModification($date);

        return $sousTache;
    }

    public function supprimer($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'DELETE FROM sous_taches
             WHERE id = :id'
        );

        return $requete->execute(array(
            ':id' => $id
        ));
    }

    public function compterParTache($tacheId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM sous_taches
             WHERE tache_id = :tache_id'
        );

        $requete->execute(array(
            ':tache_id' => $tacheId
        ));

        $donnees = $requete->fetch();

        return (int) $donnees['total'];
    }

    public function compterTermineesParTache($tacheId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM sous_taches
             WHERE tache_id = :tache_id
             AND statut = :statut'
        );

        $requete->execute(array(
            ':tache_id' => $tacheId,
            ':statut' => 'terminee'
        ));

        $donnees = $requete->fetch();

        return (int) $donnees['total'];
    }

    private function creerObjet($donnees)
    {
        $sousTache = new SousTache();

        $sousTache->setId($donnees['id']);
        $sousTache->setTacheId($donnees['tache_id']);
        $sousTache->setTitre($donnees['titre']);
        $sousTache->setStatut($donnees['statut']);
        $sousTache->setDateCreation($donnees['date_creation']);
        $sousTache->setDateModification($donnees['date_modification']);

        return $sousTache;
    }
}