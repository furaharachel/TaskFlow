<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Categorie.php';

class CategorieRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function ajouter(Categorie $categorie)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'INSERT INTO categories
            (
                nom,
                description,
                date_creation,
                date_modification
            )
            VALUES
            (
                :nom,
                :description,
                :date_creation,
                :date_modification
            )'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':nom' => $categorie->getNom(),
            ':description' => $categorie->getDescription(),
            ':date_creation' => $date,
            ':date_modification' => $date
        ));

        $categorie->setId($connection->lastInsertId());
        $categorie->setDateCreation($date);
        $categorie->setDateModification($date);

        return $categorie;
    }

    public function trouverTous()
    {
        $connection = $this->database->connect();

        $requete = $connection->query(
            'SELECT *
             FROM categories
             ORDER BY nom ASC'
        );

        $categories = array();

        while ($donnees = $requete->fetch()) {
            $categories[] = $this->creerObjet($donnees);
        }

        return $categories;
    }

    public function trouverParId($id)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM categories
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

    public function modifier(Categorie $categorie)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'UPDATE categories
             SET
                nom = :nom,
                description = :description,
                date_modification = :date_modification
             WHERE id = :id'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':nom' => $categorie->getNom(),
            ':description' => $categorie->getDescription(),
            ':date_modification' => $date,
            ':id' => $categorie->getId()
        ));

        $categorie->setDateModification($date);

        return $categorie;
    }

    public function supprimer($id)
    {
        $connection = $this->database->connect();

        try {

            $connection->beginTransaction();

            /*
             * Les tâches qui utilisent cette catégorie
             * deviennent "Sans catégorie".
             */
            $requeteTaches = $connection->prepare(
                'UPDATE taches
                 SET categorie_id = NULL
                 WHERE categorie_id = :categorie_id'
            );

            $requeteTaches->execute(array(
                ':categorie_id' => $id
            ));

            /*
             * Maintenant que les tâches ne dépendent
             * plus de la catégorie, on peut la supprimer.
             */
            $requeteCategorie = $connection->prepare(
                'DELETE FROM categories
                 WHERE id = :id'
            );

            $requeteCategorie->execute(array(
                ':id' => $id
            ));

            $supprimee = $requeteCategorie->rowCount() > 0;

            $connection->commit();

            return $supprimee;

        } catch (PDOException $exception) {

            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    private function creerObjet($donnees)
    {
        $categorie = new Categorie();

        $categorie->setId($donnees['id']);
        $categorie->setNom($donnees['nom']);
        $categorie->setDescription($donnees['description']);
        $categorie->setDateCreation($donnees['date_creation']);
        $categorie->setDateModification($donnees['date_modification']);

        return $categorie;
    }
}