<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Utilisateur.php';
require_once __DIR__ . '/../App/Repository/UtilisateurRepository.php';

if (isset($_SESSION['utilisateur_id'])) {
    header('Location: /TaskFlow/Web/index.php');
    exit;
}

$database = new Database();

$utilisateurRepository = new UtilisateurRepository($database);

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

            $utilisateur = $utilisateurRepository->trouverParEmail(
                $email
            );

            if (
                $utilisateur !== null &&
                password_verify(
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

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Connexion — TaskFlow</title>

    <link
        rel="stylesheet"
        href="../Assets/css/style.css"
    >

</head>

<body>

<main class="page">

    <div class="conteneur">

        <section class="carte">

            <div>

                <a
                    href="index.php"
                    class="logo"
                >
                    TaskFlow
                </a>

                <h1 class="titre-page">
                    Connexion
                </h1>

                <p class="sous-titre">
                    Connectez-vous à votre espace TaskFlow.
                </p>

            </div>

            <?php if ($erreur !== ''): ?>

                <div class="message">

                    <?php
                    echo htmlspecialchars(
                        $erreur,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            <?php endif; ?>

            <form
                method="post"
                action="connexion.php"
            >

                <div>

                    <label for="email">
                        Adresse e-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php
                        echo htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        maxlength="150"
                        autocomplete="email"
                        required
                    >

                </div>

                <div>

                    <label for="mot_de_passe">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        autocomplete="current-password"
                        required
                    >

                </div>

                <div>

                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Se connecter
                    </button>

                </div>

            </form>

        </section>

    </div>

</main>

</body>

</html>