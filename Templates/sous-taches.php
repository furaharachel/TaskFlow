<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sous-tâches - TaskFlow</title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/css/style.css"
    >
</head>

<body>

<header class="en-tete">

    <div class="conteneur navigation">

        <div class="logo">
            <a href="/TaskFlow/Web/index.php">
                TaskFlow
            </a>
        </div>

        <nav>

            <a href="/TaskFlow/Web/index.php">
                Tableau de bord
            </a>

            <a href="/TaskFlow/Web/taches.php">
                Mes tâches
            </a>

            <a href="/TaskFlow/Web/categories.php">
                Catégories
            </a>

            <a href="/TaskFlow/Web/deconnexion.php">
                Déconnexion
            </a>

        </nav>

    </div>

</header>

<main class="conteneur">

    <div class="page-entete">

        <div>

            <h1>
                Sous-tâches :
                <?php echo htmlspecialchars(
                    $tache->getTitre(),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </h1>

            <p>
                Découpez cette tâche en plusieurs étapes.
            </p>

        </div>

        <a
            href="/TaskFlow/Web/taches.php"
            class="bouton bouton-secondaire"
        >
            Retour aux tâches
        </a>

    </div>


    <section class="carte">

        <h2>Progression</h2>

        <div class="tache-informations">

            <div>

                <span class="information-label">
                    Sous-tâches
                </span>

                <strong>
                    <?php echo $nombreSousTaches; ?>
                </strong>

            </div>

            <div>

                <span class="information-label">
                    Terminées
                </span>

                <strong>
                    <?php echo $nombreSousTachesTerminees; ?>
                </strong>

            </div>

            <div>

                <span class="information-label">
                    Progression
                </span>

                <strong>
                    <?php echo $progression; ?> %
                </strong>

            </div>

        </div>

    </section>


    <section class="carte">

        <h2>Ajouter une sous-tâche</h2>

        <form
            method="post"
            action="/TaskFlow/Web/sous-taches.php?id=<?php echo (int) $id; ?>"
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

                <select
                    id="statut"
                    name="statut"
                >

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
                Ajouter la sous-tâche
            </button>

        </form>

    </section>


    <section class="liste-taches">

        <?php if (empty($sousTaches)): ?>

            <article class="carte vide">

                <h2>Aucune sous-tâche</h2>

                <p>
                    Commencez par ajouter une étape à cette tâche.
                </p>

            </article>

        <?php else: ?>

            <?php foreach ($sousTaches as $sousTache): ?>

                <article class="carte tache">

                    <div class="tache-entete">

                        <div>

                            <h2>
                                <?php echo htmlspecialchars(
                                    $sousTache->getTitre(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </h2>

                            <?php if (
                                $sousTache->getStatut() === 'terminee'
                            ): ?>

                                <span class="badge">
                                    Terminée
                                </span>

                            <?php else: ?>

                                <span class="badge">
                                    À faire
                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="tache-actions">

                            <a
                                href="/TaskFlow/Web/modifier-sous-tache.php?id=<?php echo (int) $sousTache->getId(); ?>"
                                class="bouton bouton-secondaire"
                            >
                                Modifier
                            </a>

                            <a
                                href="/TaskFlow/Web/supprimer-sous-tache.php?id=<?php echo (int) $sousTache->getId(); ?>"
                                class="bouton bouton-danger"
                            >
                                Supprimer
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

</body>

</html>