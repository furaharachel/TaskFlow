<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Rappel.php';

class RappelRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function ajouter(Rappel $rappel)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'INSERT INTO rappels
            (
                tache_id,
                date_rappel,
                message,
                statut,
                date_creation,
                date_modification
            )
            VALUES
            (
                :tache_id,
                :date_rappel,
                :message,
                :statut,
                :date_creation,
                :date_modification
            )'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':tache_id' => $rappel->getTacheId(),
            ':date_rappel' => $rappel->getDateRappel(),
            ':message' => $rappel->getMessage(),
            ':statut' => $rappel->getStatut(),
            ':date_creation' => $date,
            ':date_modification' => $date
        ));

        $rappel->setId($connection->lastInsertId());
        $rappel->setDateCreation($date);
        $rappel->setDateModification($date);

        return $rappel;
    }

    public function trouverParTache($tacheId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM rappels
             WHERE tache_id = :tache_id
             ORDER BY date_rappel ASC'
        );

        $requete->execute(array(
            ':tache_id' => $tacheId
        ));

        $rappels = array();

        while ($donnees = $requete->fetch()) {
            $rappels[] = $this->creerObjet($donnees);
        }

        return $rappels;
    }

    public function trouverParId($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM rappels
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

    public function modifier(Rappel $rappel)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'UPDATE rappels
             SET
                date_rappel = :date_rappel,
                message = :message,
                statut = :statut,
                date_modification = :date_modification
             WHERE id = :id'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':date_rappel' => $rappel->getDateRappel(),
            ':message' => $rappel->getMessage(),
            ':statut' => $rappel->getStatut(),
            ':date_modification' => $date,
            ':id' => $rappel->getId()
        ));

        $rappel->setDateModification($date);

        return $rappel;
    }

    public function supprimer($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'DELETE FROM rappels
             WHERE id = :id'
        );

        return $requete->execute(array(
            ':id' => $id
        ));
    }

    public function trouverActifsParUtilisateur($utilisateurId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT
                r.*
             FROM rappels r
             INNER JOIN taches t
                ON r.tache_id = t.id
             WHERE t.utilisateur_id = :utilisateur_id
             AND r.statut = "actif"
             ORDER BY r.date_rappel ASC'
        );

        $requete->execute(array(
            ':utilisateur_id' => $utilisateurId
        ));

        $rappels = array();

        while ($donnees = $requete->fetch()) {
            $rappels[] = $this->creerObjet($donnees);
        }

        return $rappels;
    }

    private function creerObjet($donnees)
    {
        $rappel = new Rappel();

        $rappel->setId($donnees['id']);
        $rappel->setTacheId($donnees['tache_id']);
        $rappel->setDateRappel($donnees['date_rappel']);
        $rappel->setMessage($donnees['message']);
        $rappel->setStatut($donnees['statut']);
        $rappel->setDateCreation($donnees['date_creation']);
        $rappel->setDateModification($donnees['date_modification']);

        return $rappel;
    }
}