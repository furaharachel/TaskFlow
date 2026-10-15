<?php

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Entity/Historique.php';

class HistoriqueRepository extends Repository
{
    public function ajouter(Historique $historique)
    {
        $connection = $this->getPdo();

        $requete = $connection->prepare(
            'INSERT INTO historique
            (
                utilisateur_id,
                tache_id,
                action,
                description,
                date_action
            )
            VALUES
            (
                :utilisateur_id,
                :tache_id,
                :action,
                :description,
                :date_action
            )'
        );

        $date = date('Y-m-d H:i:s');

        $requete->execute(array(
            ':utilisateur_id' => $historique->getUtilisateurId(),
            ':tache_id' => $historique->getTacheId(),
            ':action' => $historique->getAction(),
            ':description' => $historique->getDescription(),
            ':date_action' => $date
        ));

        $historique->setId($connection->lastInsertId());
        $historique->setDateAction($date);
        $historique->setDateCreation($date);
        $historique->setDateModification($date);

        return $historique;
    }

    public function trouverParUtilisateur($utilisateurId)
    {
        if (!$this->idValide($utilisateurId)) {
            return array();
        }

        $requete = $this->getPdo()->prepare(
            'SELECT
                h.*,
                t.titre AS tache_titre
             FROM historique h
             LEFT JOIN taches t
                ON h.tache_id = t.id
             WHERE h.utilisateur_id = :utilisateur_id
             ORDER BY h.date_action DESC,
                      h.id DESC'
        );

        $requete->execute(array(
            ':utilisateur_id' => (int) $utilisateurId
        ));

        $historiques = array();

        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $historiques[] = $this->creerObjet($donnees);
        }

        return $historiques;
    }

    public function supprimerParTache($tacheId)
    {
        if (!$this->idValide($tacheId)) {
            return false;
        }

        $requete = $this->getPdo()->prepare(
            'DELETE FROM historique
             WHERE tache_id = :tache_id'
        );

        return $requete->execute(array(
            ':tache_id' => (int) $tacheId
        ));
    }

    private function creerObjet($donnees)
    {
        $historique = new Historique();

        $historique->setId($donnees['id']);

        $historique->setUtilisateurId(
            $donnees['utilisateur_id']
        );

        $historique->setTacheId(
            $donnees['tache_id']
        );

        if (isset($donnees['tache_titre'])) {
            $historique->setTacheTitre(
                $donnees['tache_titre']
            );
        } else {
            $historique->setTacheTitre(null);
        }

        $historique->setAction($donnees['action']);
        $historique->setDescription($donnees['description']);
        $historique->setDateAction($donnees['date_action']);

        return $historique;
    }
}