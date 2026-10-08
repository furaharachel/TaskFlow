<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Tache.php';

class TacheRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function ajouter(Tache $tache)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'INSERT INTO taches
            (
                utilisateur_id,
                categorie_id,
                titre,
                description,
                statut,
                priorite,
                date_echeance,
                duree_estimee,
                date_creation,
                date_modification
            )
            VALUES
            (
                :utilisateur_id,
                :categorie_id,
                :titre,
                :description,
                :statut,
                :priorite,
                :date_echeance,
                :duree_estimee,
                :date_creation,
                :date_modification
            )'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':utilisateur_id' => $tache->getUtilisateurId(),
            ':categorie_id' => $tache->getCategorieId(),
            ':titre' => $tache->getTitre(),
            ':description' => $tache->getDescription(),
            ':statut' => $tache->getStatut(),
            ':priorite' => $tache->getPriorite(),
            ':date_echeance' => $tache->getDateEcheance(),
            ':duree_estimee' => $tache->getDureeEstimee(),
            ':date_creation' => $date,
            ':date_modification' => $date
        ));

        $tache->setId($connection->lastInsertId());
        $tache->setDateCreation($date);
        $tache->setDateModification($date);

        return $tache;
    }

    public function trouverTous()
    {
        $connection = $this->database->connect();

        $requete = $connection->query(
            'SELECT
                t.*,
                c.nom AS categorie_nom
             FROM taches t
             LEFT JOIN categories c
                ON t.categorie_id = c.id
             ORDER BY
                t.date_echeance IS NULL,
                t.date_echeance ASC,
                t.id DESC'
        );

        $taches = array();

        while ($donnees = $requete->fetch()) {
            $taches[] = $this->creerObjet($donnees);
        }

        return $taches;
    }

    public function trouverParUtilisateur($utilisateurId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT
                t.*,
                c.nom AS categorie_nom,
                (
                    SELECT COUNT(*)
                    FROM sous_taches st
                    WHERE st.tache_id = t.id
                ) AS nombre_sous_taches,
                (
                    SELECT COUNT(*)
                    FROM sous_taches st
                    WHERE st.tache_id = t.id
                    AND st.statut = "terminee"
                ) AS nombre_sous_taches_terminees
             FROM taches t
             LEFT JOIN categories c
                ON t.categorie_id = c.id
             WHERE t.utilisateur_id = :utilisateur_id
             ORDER BY
                t.date_echeance IS NULL,
                t.date_echeance ASC,
                t.id DESC'
        );

        $requete->execute(array(
            ':utilisateur_id' => $utilisateurId
        ));

        $taches = array();

        while ($donnees = $requete->fetch()) {
            $taches[] = $this->creerObjet($donnees);
        }

        return $taches;
    }

    public function trouverParId($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT
                t.*,
                c.nom AS categorie_nom,
                (
                    SELECT COUNT(*)
                    FROM sous_taches st
                    WHERE st.tache_id = t.id
                ) AS nombre_sous_taches,
                (
                    SELECT COUNT(*)
                    FROM sous_taches st
                    WHERE st.tache_id = t.id
                    AND st.statut = "terminee"
                ) AS nombre_sous_taches_terminees
             FROM taches t
             LEFT JOIN categories c
                ON t.categorie_id = c.id
             WHERE t.id = :id'
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

    public function modifier(Tache $tache)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'UPDATE taches
             SET
                utilisateur_id = :utilisateur_id,
                categorie_id = :categorie_id,
                titre = :titre,
                description = :description,
                statut = :statut,
                priorite = :priorite,
                date_echeance = :date_echeance,
                duree_estimee = :duree_estimee,
                date_modification = :date_modification
             WHERE id = :id'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':utilisateur_id' => $tache->getUtilisateurId(),
            ':categorie_id' => $tache->getCategorieId(),
            ':titre' => $tache->getTitre(),
            ':description' => $tache->getDescription(),
            ':statut' => $tache->getStatut(),
            ':priorite' => $tache->getPriorite(),
            ':date_echeance' => $tache->getDateEcheance(),
            ':duree_estimee' => $tache->getDureeEstimee(),
            ':date_modification' => $date,
            ':id' => $tache->getId()
        ));

        $tache->setDateModification($date);

        return $tache;
    }

    public function supprimer($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'DELETE FROM taches
             WHERE id = :id'
        );

        return $requete->execute(array(
            ':id' => $id
        ));
    }

    public function compterParStatut($statut)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM taches
             WHERE statut = :statut'
        );

        $requete->execute(array(
            ':statut' => $statut
        ));

        $donnees = $requete->fetch();

        return (int) $donnees['total'];
    }

    public function compterParUtilisateurEtStatut($utilisateurId, $statut)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM taches
             WHERE utilisateur_id = :utilisateur_id
             AND statut = :statut'
        );

        $requete->execute(array(
            ':utilisateur_id' => $utilisateurId,
            ':statut' => $statut
        ));

        $donnees = $requete->fetch();

        return (int) $donnees['total'];
    }

    public function compterParUtilisateur($utilisateurId)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT COUNT(*) AS total
             FROM taches
             WHERE utilisateur_id = :utilisateur_id'
        );

        $requete->execute(array(
            ':utilisateur_id' => $utilisateurId
        ));

        $donnees = $requete->fetch();

        return (int) $donnees['total'];
    }

    private function creerObjet($donnees)
    {
        $tache = new Tache();

        $tache->setId($donnees['id']);
        $tache->setUtilisateurId($donnees['utilisateur_id']);
        $tache->setCategorieId($donnees['categorie_id']);

        if (isset($donnees['categorie_nom'])) {
            $tache->setCategorieNom($donnees['categorie_nom']);
        } else {
            $tache->setCategorieNom(null);
        }

        $tache->setTitre($donnees['titre']);
        $tache->setDescription($donnees['description']);
        $tache->setStatut($donnees['statut']);
        $tache->setPriorite($donnees['priorite']);
        $tache->setDateEcheance($donnees['date_echeance']);
        $tache->setDureeEstimee($donnees['duree_estimee']);
        $tache->setDateCreation($donnees['date_creation']);
        $tache->setDateModification($donnees['date_modification']);

        if (isset($donnees['nombre_sous_taches'])) {
            $tache->setNombreSousTaches(
                $donnees['nombre_sous_taches']
            );
        } else {
            $tache->setNombreSousTaches(0);
        }

        if (isset($donnees['nombre_sous_taches_terminees'])) {
            $tache->setNombreSousTachesTerminees(
                $donnees['nombre_sous_taches_terminees']
            );
        } else {
            $tache->setNombreSousTachesTerminees(0);
        }

        return $tache;
    }
}