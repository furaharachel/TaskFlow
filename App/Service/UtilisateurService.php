<?php

require_once __DIR__ . '/../Repository/UtilisateurRepository.php';
require_once __DIR__ . '/../Validation/UtilisateurValidator.php';
require_once __DIR__ . '/../Entity/Utilisateur.php';
require_once __DIR__ . '/../../Config/Database.php';

class UtilisateurService
{
    private $utilisateurRepository;
    private $validator;

    public function __construct(Database $database)
    {
        $this->utilisateurRepository =
            new UtilisateurRepository($database);

        $this->validator = new UtilisateurValidator();
    }

    /**
     * Authentifie un utilisateur.
     */
    public function authentifier($email, $motDePasse)
    {
        if (!is_scalar($email)) {
            $email = '';
        }

        if (!is_scalar($motDePasse)) {
            $motDePasse = '';
        }

        $email = trim((string) $email);
        $motDePasse = (string) $motDePasse;

        if ($email === '') {
            return array(
                'succes' => false,
                'erreur' => 'Veuillez saisir votre adresse e-mail.',
                'utilisateur' => null
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array(
                'succes' => false,
                'erreur' => 'Veuillez saisir une adresse e-mail valide.',
                'utilisateur' => null
            );
        }

        if ($motDePasse === '') {
            return array(
                'succes' => false,
                'erreur' => 'Veuillez saisir votre mot de passe.',
                'utilisateur' => null
            );
        }

        $utilisateur =
            $this->utilisateurRepository->trouverParEmail($email);

        if (
            $utilisateur === null
            || !password_verify(
                $motDePasse,
                $utilisateur->getMotDePasse()
            )
        ) {
            return array(
                'succes' => false,
                'erreur' =>
                    'Adresse e-mail ou mot de passe incorrect.',
                'utilisateur' => null
            );
        }

        return array(
            'succes' => true,
            'erreur' => '',
            'utilisateur' => $utilisateur
        );
    }

    /**
     * Valide et crée un compte utilisateur.
     */
    public function inscrire(
        $nom,
        $prenom,
        $email,
        $motDePasse,
        $confirmation
    ) {
        if (!is_scalar($nom)) {
            $nom = '';
        }

        if (!is_scalar($prenom)) {
            $prenom = '';
        }

        if (!is_scalar($email)) {
            $email = '';
        }

        if (!is_scalar($motDePasse)) {
            $motDePasse = '';
        }

        if (!is_scalar($confirmation)) {
            $confirmation = '';
        }

        $nom = trim((string) $nom);
        $prenom = trim((string) $prenom);
        $email = trim((string) $email);
        $motDePasse = (string) $motDePasse;
        $confirmation = (string) $confirmation;

        $erreurs = $this->validator->validerInscription(
            $nom,
            $prenom,
            $email,
            $motDePasse,
            $confirmation
        );

        if (!empty($erreurs)) {
            return array(
                'succes' => false,
                'erreur' => reset($erreurs),
                'erreurs' => $erreurs
            );
        }

        $utilisateurExistant =
            $this->utilisateurRepository->trouverParEmail($email);

        if ($utilisateurExistant !== null) {
            return array(
                'succes' => false,
                'erreur' =>
                    'Un compte existe déjà avec cette adresse e-mail.',
                'erreurs' => array(
                    'email' =>
                        'Un compte existe déjà avec cette adresse e-mail.'
                )
            );
        }

        $motDePasseHash = password_hash(
            $motDePasse,
            PASSWORD_DEFAULT
        );

        if ($motDePasseHash === false) {
            return array(
                'succes' => false,
                'erreur' =>
                    'Impossible de sécuriser le mot de passe.',
                'erreurs' => array()
            );
        }

        $utilisateur = new Utilisateur();

        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setEmail($email);
        $utilisateur->setMotDePasse($motDePasseHash);

        $this->utilisateurRepository->ajouter($utilisateur);

        return array(
            'succes' => true,
            'erreur' => '',
            'erreurs' => array(),
            'utilisateur' => $utilisateur
        );
    }
}