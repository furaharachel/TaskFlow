<?php

require_once __DIR__ . '/../App/Session.php';

Session::demarrer();

if (Session::estConnecte()) {
    header('Location: /TaskFlow/Web/index.php');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';

$database = new Database();

$erreur = '';
$succes = '';

$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = isset($_POST['nom'])
        ? trim($_POST['nom'])
        : '';

    $prenom = isset($_POST['prenom'])
        ? trim($_POST['prenom'])
        : '';

    $email = isset($_POST['email'])
        ? trim($_POST['email'])
        : '';

    $motDePasse = isset($_POST['mot_de_passe'])
        ? $_POST['mot_de_passe']
        : '';

    $confirmation = isset($_POST['confirmation'])
        ? $_POST['confirmation']
        : '';

    if ($nom === '') {

        $erreur = 'Veuillez saisir votre nom.';

    } elseif (strlen($nom) > 100) {

        $erreur = 'Le nom ne peut pas dépasser 100 caractères.';

    } elseif ($prenom === '') {

        $erreur = 'Veuillez saisir votre prénom.';

    } elseif (strlen($prenom) > 100) {

        $erreur = 'Le prénom ne peut pas dépasser 100 caractères.';

    } elseif ($email === '') {

        $erreur = 'Veuillez saisir votre adresse e-mail.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur = 'Veuillez saisir une adresse e-mail valide.';

    } elseif ($motDePasse === '') {

        $erreur = 'Veuillez saisir un mot de passe.';

    } elseif (strlen($motDePasse) < 6) {

        $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';

    } elseif ($confirmation === '') {

        $erreur = 'Veuillez confirmer votre mot de passe.';

    } elseif ($motDePasse !== $confirmation) {

        $erreur = 'Les deux mots de passe ne correspondent pas.';

    } else {

        try {

            $connection = $database->connect();

            $requeteVerification = $connection->prepare(
                'SELECT id
                 FROM utilisateurs
                 WHERE email = :email
                 LIMIT 1'
            );

            $requeteVerification->execute(array(
                ':email' => $email
            ));

            $utilisateurExiste = $requeteVerification->fetch();

            if ($utilisateurExiste) {

                $erreur =
                    'Un compte existe déjà avec cette adresse e-mail.';

            } else {

                $motDePasseHash = password_hash(
                    $motDePasse,
                    PASSWORD_DEFAULT
                );

                $date = date('Y-m-d H:i:s');

                $requete = $connection->prepare(
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
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':mot_de_passe' => $motDePasseHash,
                    ':date_creation' => $date,
                    ':date_modification' => $date
                ));

                header('Location: /TaskFlow/Web/connexion.php');
                exit;
            }

        } catch (PDOException $exception) {

            $erreur =
                'Impossible de créer le compte pour le moment.';
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

    <title>Créer un compte — TaskFlow</title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/css/style.css"
    >

</head>

<body>

<main class="page">

    <div class="conteneur">

        <section class="carte">

            <div>

                <a
                    href="/TaskFlow/Web/connexion.php"
                    class="logo"
                >
                    TaskFlow
                </a>

                <h1 class="titre-page">
                    Créer un compte
                </h1>

                <p class="sous-titre">
                    Créez votre espace personnel TaskFlow.
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
                action="inscription.php"
            >

                <div>

                    <label for="prenom">
                        Prénom
                    </label>

                    <input
                        type="text"
                        id="prenom"
                        name="prenom"
                        value="<?php
                            echo htmlspecialchars(
                                $prenom,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        maxlength="100"
                        autocomplete="given-name"
                        required
                    >

                </div>

                <div>

                    <label for="nom">
                        Nom
                    </label>

                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="<?php
                            echo htmlspecialchars(
                                $nom,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        maxlength="100"
                        autocomplete="family-name"
                        required
                    >

                </div>

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
                        autocomplete="new-password"
                        required
                    >

                </div>

                <div>

                    <label for="confirmation">
                        Confirmer le mot de passe
                    </label>

                    <input
                        type="password"
                        id="confirmation"
                        name="confirmation"
                        autocomplete="new-password"
                        required
                    >

                </div>

                <div>

                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer mon compte
                    </button>

                </div>

            </form>

            <p>

                Vous avez déjà un compte ?

                <a
                    href="/TaskFlow/Web/connexion.php"
                    class="lien"
                >
                    Se connecter
                </a>

            </p>

        </section>

    </div>

</main>

</body>

</html>