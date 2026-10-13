<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Entity/Utilisateur.php';
require_once __DIR__ . '/../App/Repository/UtilisateurRepository.php';

if (isset($_SESSION['utilisateur_id'])) {
    header('Location: /TaskFlow/Web/index.php');
    exit;
}

$erreur = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = isset($_POST['email'])
        ? trim($_POST['email'])
        : '';

    $motDePasse = isset($_POST['mot_de_passe'])
        ? $_POST['mot_de_passe']
        : '';

    if ($email === '') {

        $erreur = 'Veuillez saisir votre adresse e-mail.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur = 'Veuillez saisir une adresse e-mail valide.';

    } elseif ($motDePasse === '') {

        $erreur = 'Veuillez saisir votre mot de passe.';

    } else {

        try {

            $database = new Database();

            $utilisateurRepository =
                new UtilisateurRepository($database);

            $utilisateur =
                $utilisateurRepository->trouverParEmail($email);

            if (
                $utilisateur !== null
                && password_verify(
                    $motDePasse,
                    $utilisateur->getMotDePasse()
                )
            ) {

                session_regenerate_id(true);

                $_SESSION['utilisateur_id'] =
                    $utilisateur->getId();

                $_SESSION['utilisateur_nom'] =
                    $utilisateur->getNom();

                $_SESSION['utilisateur_prenom'] =
                    $utilisateur->getPrenom();

                $_SESSION['utilisateur_email'] =
                    $utilisateur->getEmail();

                header('Location: /TaskFlow/Web/index.php');
                exit;

            } else {

                $erreur =
                    'Adresse e-mail ou mot de passe incorrect.';
            }

        } catch (PDOException $exception) {

            $erreur =
                'Impossible de vous connecter pour le moment.';
        }
    }
}

require __DIR__ . '/../Templates/connexion.php';