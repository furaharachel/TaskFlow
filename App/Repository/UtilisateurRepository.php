<?php

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Entity/Utilisateur.php';

class UtilisateurRepository extends Repository
{
    /**
     * Recherche un utilisateur par son adresse e-mail.
     */
    public function trouverParEmail($email)
    {
        $requete = $this->getPdo()->prepare(
            'SELECT *
             FROM utilisateurs
             WHERE email = :email
             LIMIT 1'
        );

        $requete->execute(array(
            ':email' => $email
        ));

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$donnees) {
            return null;
        }

        return $this->creerObjet($donnees);
    }

    /**
     * Enregistre un nouvel utilisateur.
     * Le mot de passe reçu doit déjà être haché.
     */
    public function ajouter(Utilisateur $utilisateur)
    {
        $connexion = $this->getPdo();

        $date = date('Y-m-d H:i:s');

        $requete = $connexion->prepare(
            'INSERT INTO utilisateurs
            (
                nom,
                prenom,
                email,
                mot_de_passe,
                date_creation,
                date_modification
            )
            VALUES
            (
                :nom,
                :prenom,
                :email,
                :mot_de_passe,
                :date_creation,
                :date_modification
            )'
        );

        $requete->execute(array(
            ':nom' => $utilisateur->getNom(),
            ':prenom' => $utilisateur->getPrenom(),
            ':email' => $utilisateur->getEmail(),
            ':mot_de_passe' => $utilisateur->getMotDePasse(),
            ':date_creation' => $date,
            ':date_modification' => $date
        ));

        $utilisateur->setId($connexion->lastInsertId());
        $utilisateur->setDateCreation($date);
        $utilisateur->setDateModification($date);

        return $utilisateur;
    }

    /**
     * Transforme les données SQL en entité Utilisateur.
     */
    private function creerObjet($donnees)
    {
        $utilisateur = new Utilisateur();

        $utilisateur->setId($donnees['id']);
        $utilisateur->setNom($donnees['nom']);
        $utilisateur->setPrenom($donnees['prenom']);
        $utilisateur->setEmail($donnees['email']);
        $utilisateur->setMotDePasse($donnees['mot_de_passe']);
        $utilisateur->setDateCreation($donnees['date_creation']);
        $utilisateur->setDateModification(
            $donnees['date_modification']
        );

        return $utilisateur;
    }
}