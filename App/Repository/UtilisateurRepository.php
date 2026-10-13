<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Utilisateur.php';

class UtilisateurRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function trouverParEmail($email)
    {
        $connection = $this->database->connect();

        $requete = $connection->prepare(
            'SELECT *
             FROM utilisateurs
             WHERE email = :email
             LIMIT 1'
        );

        $requete->execute(array(
            ':email' => $email
        ));

        $donnees = $requete->fetch();

        if (!$donnees) {
            return null;
        }

        return $this->creerObjet($donnees);
    }

    private function creerObjet($donnees)
    {
        $utilisateur = new Utilisateur();

        $utilisateur->setId($donnees['id']);
        $utilisateur->setNom($donnees['nom']);
        $utilisateur->setPrenom($donnees['prenom']);
        $utilisateur->setEmail($donnees['email']);
        $utilisateur->setMotDePasse($donnees['mot_de_passe']);
        $utilisateur->setDateCreation($donnees['date_creation']);
        $utilisateur->setDateModification($donnees['date_modification']);

        return $utilisateur;
    }
}