<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Entity/Tache.php';

/**
 * Repository des tâches.
 *
 * Toutes les opérations SQL concernant les tâches
 * sont regroupées dans cette classe.
 */
class TacheRepository extends Repository
{
    /**
     * Ajoute une tâche en base de données.
     *
     * @param Tache $tache
     * @return Tache
     */
    public function ajouter(Tache $tache)
    {
        $requete = $this->pdo->prepare(
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

        $tache->setId($this->pdo->lastInsertId());
        $tache->setDateCreation($date);
        $tache->setDateModification($date);

        return $tache;
    }

    /**
     * Récupère toutes les tâches.
     *
     * @return array
     */
    public function trouverTous()
    {
        $requete = $this->pdo->query(
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

    /**
     * Récupère les tâches d'un utilisateur.
     *
     * @param int $utilisateurId
     * @return array
     */
    public function trouverParUtilisateur($utilisateurId)
    {
        $requete = $this->pdo->prepare(
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

    /**
     * Récupère une tâche par son identifiant.
     *
     * @param int $id
     * @return Tache|null
     */
    public function trouverParId($id)
    {
        $requete = $this->pdo->prepare(
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

    /**
     * Modifie une tâche.
     *
     * @param Tache $tache
     * @return Tache
     */
    public function modifier(Tache $tache)
    {
        $requete = $this->pdo->prepare(
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

    /**
     * Supprime une tâche.
     *
     * @param int $id
     * @return bool
     */
    public function supprimer($id)
    {
        $requete = $this->pdo->prepare(
            'DELETE FROM taches
             WHERE id = :id'
        );

        return $requete->execute(array(
            ':id' => $id
        ));
    }

    /**
     * Compte les tâches selon leur statut.
     *
     * @param string $statut
     * @return int
     */
    public function compterParStatut($statut)
    {
        $requete = $this->pdo->prepare(
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

    /**
     * Compte les tâches d'un utilisateur selon leur statut.
     *
     * @param int $utilisateurId
     * @param string $statut
     * @return int
     */
    public function compterParUtilisateurEtStatut(
        $utilisateurId,
        $statut
    ) {
        $requete = $this->pdo->prepare(
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

    /**
     * Compte toutes les tâches d'un utilisateur.
     *
     * @param int $utilisateurId
     * @return int
     */
    public function compterParUtilisateur($utilisateurId)
    {
        $requete = $this->pdo->prepare(
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

    /**
     * Transforme les données SQL en objet Tache.
     *
     * @param array $donnees
     * @return Tache
     */
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