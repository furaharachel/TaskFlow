<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une sous-tâche - TaskFlow</title>
    <link rel="stylesheet" href="/TaskFlow/Assets/css/style.css">
</head>

<body>

<header class="en-tete">

    <div class="conteneur navigation">

        <div class="logo">
            <a href="/TaskFlow/Web/index.php">TaskFlow</a>
        </div>

        <nav>
            <a href="/TaskFlow/Web/index.php">Tableau de bord</a>
            <a href="/TaskFlow/Web/taches.php">Mes tâches</a>
            <a href="/TaskFlow/Web/categories.php">Catégories</a>
            <a href="/TaskFlow/Web/deconnexion.php">Déconnexion</a>
        </nav>

    </div>

</header>

<main class="conteneur">

    <div class="page-entete">

        <div>
            <h1>Modifier la sous-tâche</h1>

            <p>
                Tâche principale :
                <strong>
                    <?php echo htmlspecialchars(
                        $tache->getTitre(),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </p>
        </div>

        <a
            href="/TaskFlow/Web/sous-taches.php?id=<?php echo (int) $tache->getId(); ?>"
            class="bouton bouton-secondaire"
        >
            Retour
        </a>

    </div>

    <section class="carte">

        <form
            method="post"
            action="/TaskFlow/Web/modifier-sous-tache.php?id=<?php echo (int) $id; ?>"
        >

            <div class="champ">

                <label for="titre">
                    Titre
                </label>

                <input
                    type="text"
                    id="titre"
                    name="titre"
                    value="<?php echo htmlspecialchars(
                        $titre,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                >

                <?php if ($erreurTitre !== ''): ?>

                    <p class="erreur">
                        <?php echo htmlspecialchars(
                            $erreurTitre,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </p>

                <?php endif; ?>

            </div>

            <div class="champ">

                <label for="statut">
                    Statut
                </label>

                <select id="statut" name="statut">

                    <option
                        value="a_faire"
                        <?php
                        if ($statut === 'a_faire') {
                            echo 'selected';
                        }
                        ?>
                    >
                        À faire
                    </option>

                    <option
                        value="terminee"
                        <?php
                        if ($statut === 'terminee') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Terminée
                    </option>

                </select>

                <?php if ($erreurStatut !== ''): ?>

                    <p class="erreur">
                        <?php echo htmlspecialchars(
                            $erreurStatut,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </p>

                <?php endif; ?>

            </div>

            <button
                type="submit"
                class="bouton bouton-principal"
            >
                Enregistrer les modifications
            </button>

        </form>

    </section>

</main>

</body>
</html>