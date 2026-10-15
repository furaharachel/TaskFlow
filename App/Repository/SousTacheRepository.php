<?php

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Entity/SousTache.php';

class SousTacheRepository extends Repository
{
    public function ajouter(SousTache $sousTache)
    {
        $connection = $this->getPdo();

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
        if (!$this->idValide($tacheId)) {
            return array();
        }

        $requete = $this->getPdo()->prepare(
            'SELECT *
             FROM sous_taches
             WHERE tache_id = :tache_id
             ORDER BY id ASC'
        );

        $requete->execute(array(
            ':tache_id' => (int) $tacheId
        ));

        $sousTaches = array();

        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $sousTaches[] = $this->creerObjet($donnees);
        }

        return $sousTaches;
    }

    public function trouverParId($id)
    {
        if (!$this->idValide($id)) {
            return null;
        }

        $requete = $this->getPdo()->prepare(
            'SELECT *
             FROM sous_taches
             WHERE id = :id'
        );

        $requete->execute(array(
            ':id' => (int) $id
        ));

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$donnees) {
            return null;
        }

        return $this->creerObjet($donnees);
    }

    public function modifier(SousTache $sousTache)
    {
        $connection = $this->getPdo();

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
        if (!$this->idValide($id)) {
            return false;
        }

        $requete = $this->getPdo()->prepare(
            'DELETE FROM sous_taches
             WHERE id = :id'
        );

        return $requete->execute(array(
            ':id' => (int) $id
        ));
    }

    public function compterParTache($tacheId)
    {
        if (!$this->idValide($tacheId)) {
            return 0;
        }

        $requete = $this->getPdo()->prepare(
            'SELECT COUNT(*) AS total
             FROM sous_taches
             WHERE tache_id = :tache_id'
        );

        $requete->execute(array(
            ':tache_id' => (int) $tacheId
        ));

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        return (int) $donnees['total'];
    }

    public function compterTermineesParTache($tacheId)
    {
        if (!$this->idValide($tacheId)) {
            return 0;
        }

        $requete = $this->getPdo()->prepare(
            'SELECT COUNT(*) AS total
             FROM sous_taches
             WHERE tache_id = :tache_id
             AND statut = :statut'
        );

        $requete->execute(array(
            ':tache_id' => (int) $tacheId,
            ':statut' => 'terminee'
        ));

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

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