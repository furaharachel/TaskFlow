<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mes tâches - TaskFlow</title>

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

            <span class="sur-titre">
                Gestion
            </span>

            <h1>
                Mes tâches
            </h1>

            <p>
                Consultez et gérez toutes vos tâches.
            </p>

        </div>

        <a
            href="/TaskFlow/Web/creer-tache.php"
            class="bouton bouton-principal"
        >
            Nouvelle tâche
        </a>

    </div>


    <section>

        <div class="section-entete">

            <h2>
                Toutes mes tâches
            </h2>

            <p>
                <?php echo count($taches); ?> tâche(s) enregistrée(s).
            </p>

        </div>


        <?php if (empty($taches)): ?>

            <article class="carte vide">

                <h2>
                    Aucune tâche
                </h2>

                <p>
                    Vous n'avez encore enregistré aucune tâche.
                </p>

                <a
                    href="/TaskFlow/Web/creer-tache.php"
                    class="bouton bouton-principal"
                >
                    Créer ma première tâche
                </a>

            </article>

        <?php else: ?>


            <div class="liste-taches">


                <?php foreach ($taches as $tache): ?>


                    <article class="carte tache">


                        <div class="tache-entete">

                            <div>

                                <h3>

                                    <?php echo htmlspecialchars(
                                        $tache->getTitre(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </h3>

                            </div>

                        </div>


                        <?php if (
                            $tache->getDescription() !== null &&
                            trim($tache->getDescription()) !== ''
                        ): ?>

                            <p class="tache-description">

                                <?php echo nl2br(
                                    htmlspecialchars(
                                        $tache->getDescription(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ); ?>

                            </p>

                        <?php endif; ?>


                        <div class="tache-informations">


                            <div>

                                <span class="information-label">
                                    Catégorie
                                </span>

                                <strong>

                                    <?php

                                    if (
                                        $tache->getCategorieNom() !== null &&
                                        trim($tache->getCategorieNom()) !== ''
                                    ) {

                                        echo htmlspecialchars(
                                            $tache->getCategorieNom(),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                    } else {

                                        echo 'Sans catégorie';

                                    }

                                    ?>

                                </strong>

                            </div>


                            <div>

                                <span class="information-label">
                                    Statut
                                </span>

                                <strong>

                                    <?php

                                    if (
                                        $tache->getStatut() === 'a_faire'
                                    ) {

                                        echo 'À faire';

                                    } elseif (
                                        $tache->getStatut() === 'en_cours'
                                    ) {

                                        echo 'En cours';

                                    } else {

                                        echo 'Terminée';

                                    }

                                    ?>

                                </strong>

                            </div>


                            <div>

                                <span class="information-label">
                                    Priorité
                                </span>

                                <strong>

                                    <?php

                                    if (
                                        $tache->getPriorite() === 'basse'
                                    ) {

                                        echo 'Basse';

                                    } elseif (
                                        $tache->getPriorite() === 'haute'
                                    ) {

                                        echo 'Haute';

                                    } elseif (
                                        $tache->getPriorite() === 'urgente'
                                    ) {

                                        echo 'Urgente';

                                    } else {

                                        echo 'Normale';

                                    }

                                    ?>

                                </strong>

                            </div>


                            <?php if (
                                $tache->getDateEcheance() !== null &&
                                $tache->getDateEcheance() !== ''
                            ): ?>

                                <div>

                                    <span class="information-label">
                                        Échéance
                                    </span>

                                    <strong>

                                        <?php echo htmlspecialchars(
                                            $tache->getDateEcheance(),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </strong>

                                </div>

                            <?php endif; ?>


                            <?php if (
                                $tache->getDureeEstimee() !== null &&
                                $tache->getDureeEstimee() !== ''
                            ): ?>

                                <div>

                                    <span class="information-label">
                                        Durée
                                    </span>

                                    <strong>

                                        <?php echo (int) $tache->getDureeEstimee(); ?>
                                        minutes

                                    </strong>

                                </div>

                            <?php endif; ?>


                        </div>


                        <?php if (
                            $tache->getNombreSousTaches() > 0
                        ): ?>

                            <div class="progression-tache">

                                <div class="progression-entete">

                                    <span>
                                        Sous-tâches
                                    </span>

                                    <strong>
                                        <?php echo $tache->getProgression(); ?> %
                                    </strong>

                                </div>

                                <div class="progression-barre">

                                    <div
                                        class="progression-valeur"
                                        style="width: <?php echo $tache->getProgression(); ?>%;"
                                    ></div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <div class="tache-actions">


                            <a
                                href="/TaskFlow/Web/sous-taches.php?id=<?php echo (int) $tache->getId(); ?>"
                                class="bouton bouton-secondaire"
                            >
                                Sous-tâches
                            </a>


                            <a
                                href="/TaskFlow/Web/modifier-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                class="bouton bouton-secondaire"
                            >
                                Modifier
                            </a>


                            <a
                                href="/TaskFlow/Web/supprimer-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                class="bouton bouton-danger"
                            >
                                Supprimer
                            </a>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>


</main>

</body>

</html>