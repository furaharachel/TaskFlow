<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Categorie.php';
require_once __DIR__ . '/../App/CategorieValidator.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

$database = new Database();
$repository = new CategorieRepository($database);
$validator = new CategorieValidator();

$erreurs = array();
$message = '';

$nom = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = isset($_POST['nom'])
        ? trim($_POST['nom'])
        : '';

    $description = isset($_POST['description'])
        ? trim($_POST['description'])
        : '';

    $categorie = new Categorie();

    $categorie->setNom($nom);
    $categorie->setDescription($description);

    if ($validator->valider($categorie)) {

        try {

            $repository->ajouter($categorie);

            header('Location: categories.php');
            exit;

        } catch (PDOException $exception) {

            $message = 'Impossible de créer la catégorie.';
        }

    } else {

        $erreurs = $validator->getErreurs();
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

    <title>Créer une catégorie — TaskFlow</title>

    <link
        rel="stylesheet"
        href="../Assets/css/style.css"
    >

</head>

<body>

<header class="entete">

    <div class="conteneur entete-interieur">

        <a href="index.php" class="logo">
            TaskFlow
        </a>

        <nav class="navigation">

            <a href="index.php">
                Tableau de bord
            </a>

            <a href="taches.php">
                Mes tâches
            </a>

            <a href="categories.php" class="actif">
                Catégories
            </a>

        </nav>

    </div>

</header>


<main class="page">

    <div class="conteneur">

        <h1 class="titre-page">
            Nouvelle catégorie
        </h1>

        <p class="sous-titre">
            Créez une catégorie pour mieux organiser vos tâches.
        </p>


        <?php if ($message !== ''): ?>

            <div class="message">

                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <section class="carte">

            <form
                method="post"
                action="creer-categorie.php"
            >

                <div>

                    <label for="nom">
                        Nom de la catégorie
                    </label>

                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        maxlength="100"
                        value="<?php
                        echo htmlspecialchars(
                            $nom,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        required
                    >

                    <?php if (isset($erreurs['nom'])): ?>

                        <p>

                            <?php
                            echo htmlspecialchars(
                                $erreurs['nom'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </p>

                    <?php endif; ?>

                </div>


                <div>

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                    ><?php
                    echo htmlspecialchars(
                        $description,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?></textarea>

                </div>


                <div>

                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer la catégorie
                    </button>

                    <a
                        href="categories.php"
                        class="bouton bouton-secondaire"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </section>

    </div>

</main>

</body>

</html>