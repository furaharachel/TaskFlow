<?php

require_once __DIR__ . '/Validator.php';

/**
 * Valide les données de création d'un compte utilisateur.
 * Compatible avec PHP 5.5.12.
 */
class UtilisateurValidator
{
    public function validerInscription(
        $nom,
        $prenom,
        $email,
        $motDePasse,
        $confirmation
    ) {
        $erreurs = array();

        $nom = trim((string) $nom);
        $prenom = trim((string) $prenom);
        $email = trim((string) $email);

        if (!Validator::requis($nom)) {
            $erreurs['nom'] = 'Veuillez saisir votre nom.';
        } elseif (!Validator::longueurMaximale($nom, 100)) {
            $erreurs['nom'] =
                'Le nom ne peut pas dépasser 100 caractères.';
        }

        if (!Validator::requis($prenom)) {
            $erreurs['prenom'] = 'Veuillez saisir votre prénom.';
        } elseif (!Validator::longueurMaximale($prenom, 100)) {
            $erreurs['prenom'] =
                'Le prénom ne peut pas dépasser 100 caractères.';
        }

        if (!Validator::requis($email)) {
            $erreurs['email'] =
                'Veuillez saisir votre adresse e-mail.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] =
                'Veuillez saisir une adresse e-mail valide.';
        } elseif (!Validator::longueurMaximale($email, 150)) {
            $erreurs['email'] =
                'L’adresse e-mail ne peut pas dépasser 150 caractères.';
        }

        if ($motDePasse === '') {
            $erreurs['mot_de_passe'] =
                'Veuillez saisir un mot de passe.';
        } elseif (strlen($motDePasse) < 6) {
            $erreurs['mot_de_passe'] =
                'Le mot de passe doit contenir au moins 6 caractères.';
        }

        if ($confirmation === '') {
            $erreurs['confirmation'] =
                'Veuillez confirmer votre mot de passe.';
        } elseif ($motDePasse !== $confirmation) {
            $erreurs['confirmation'] =
                'Les deux mots de passe ne correspondent pas.';
        }

        return $erreurs;
    }
}