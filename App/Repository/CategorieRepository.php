<?php

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Entity/Categorie.php';

class CategorieRepository extends Repository
{
    public function ajouter(Categorie $categorie)
    {
        $connection = $this->getPdo();

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
        $requete = $this->getPdo()->query(
            'SELECT *
             FROM categories
             ORDER BY nom ASC'
        );

        $categories = array();

        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $this->creerObjet($donnees);
        }

        return $categories;
    }

    public function trouverParId($id)
    {
        if (!$this->idValide($id)) {
            return null;
        }

        $requete = $this->getPdo()->prepare(
            'SELECT *
             FROM categories
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

    public function modifier(Categorie $categorie)
    {
        $connection = $this->getPdo();

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
        if (!$this->idValide($id)) {
            return false;
        }

        $connection = $this->getPdo();

        try {
            $connection->beginTransaction();

            $requeteTaches = $connection->prepare(
                'UPDATE taches
                 SET categorie_id = NULL
                 WHERE categorie_id = :categorie_id'
            );

            $requeteTaches->execute(array(
                ':categorie_id' => (int) $id
            ));

            $requeteCategorie = $connection->prepare(
                'DELETE FROM categories
                 WHERE id = :id'
            );

            $requeteCategorie->execute(array(
                ':id' => (int) $id
            ));

            $supprimee = $requeteCategorie->rowCount() > 0;

            $connection->commit();

            return $supprimee;

        } catch (Exception $exception) {
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