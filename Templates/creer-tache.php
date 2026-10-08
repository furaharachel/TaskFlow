<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Nouvelle tâche — TaskFlow</title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/css/style.css"
    >

</head>

<body>

<header class="entete">

    <div class="conteneur entete-interieur">

        <a
            href="/TaskFlow/Web/index.php"
            class="logo"
        >
            TaskFlow
        </a>

        <nav class="navigation">

            <a href="/TaskFlow/Web/index.php">
                Tableau de bord
            </a>

            <a
                href="/TaskFlow/Web/taches.php"
                class="actif"
            >
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

<main class="page">

    <div class="conteneur">

        <section class="introduction">

            <div>

                <p class="sur-titre">
                    Gestion des tâches
                </p>

                <h1 class="titre-page">
                    Nouvelle tâche
                </h1>

                <p class="sous-titre">
                    Ajoutez une nouvelle tâche à votre espace.
                </p>

            </div>

        </section>

        <?php if ($message !== ''): ?>

            <div class="message">
                <?php echo $message; ?>
            </div>

        <?php endif; ?>

        <section class="carte">

            <form
                method="post"
                action="/TaskFlow/Web/creer-tache.php"
            >

                <div>

                    <label for="titre">
                        Titre
                    </label>

                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        value="<?php echo $titre; ?>"
                        maxlength="150"
                        required
                    >

                    <?php if ($erreurTitre !== ''): ?>

                        <p class="message">
                            <?php echo $erreurTitre; ?>
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
                        rows="5"
                    ><?php echo $description; ?></textarea>

                </div>

                <div>

                    <label for="categorie_id">
                        Catégorie
                    </label>

                    <select
                        id="categorie_id"
                        name="categorie_id"
                    >

                        <option value="">
                            Sans catégorie
                        </option>

                        <?php foreach ($categories as $categorie): ?>

                            <option
                                value="<?php echo (int) $categorie->getId(); ?>"
                                <?php
                                if (
                                    (string) $categorieId ===
                                    (string) $categorie->getId()
                                ) {
                                    echo 'selected';
                                }
                                ?>
                            >
                                <?php echo htmlspecialchars(
                                    $categorie->getNom(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div>

                    <label for="statut">
                        Statut
                    </label>

                    <select
                        id="statut"
                        name="statut"
                        required
                    >

                        <option
                            value="a_faire"
                            <?php
                            echo $statut === 'a_faire'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            À faire
                        </option>

                        <option
                            value="en_cours"
                            <?php
                            echo $statut === 'en_cours'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            En cours
                        </option>

                        <option
                            value="terminee"
                            <?php
                            echo $statut === 'terminee'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Terminée
                        </option>

                    </select>

                    <?php if ($erreurStatut !== ''): ?>

                        <p class="message">
                            <?php echo $erreurStatut; ?>
                        </p>

                    <?php endif; ?>

                </div>

                <div>

                    <label for="priorite">
                        Priorité
                    </label>

                    <select
                        id="priorite"
                        name="priorite"
                        required
                    >

                        <option
                            value="basse"
                            <?php
                            echo $priorite === 'basse'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Basse
                        </option>

                        <option
                            value="normale"
                            <?php
                            echo $priorite === 'normale'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Normale
                        </option>

                        <option
                            value="haute"
                            <?php
                            echo $priorite === 'haute'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Haute
                        </option>

                        <option
                            value="urgente"
                            <?php
                            echo $priorite === 'urgente'
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Urgente
                        </option>

                    </select>

                    <?php if ($erreurPriorite !== ''): ?>

                        <p class="message">
                            <?php echo $erreurPriorite; ?>
                        </p>

                    <?php endif; ?>

                </div>

                <div>

                    <label for="date_echeance">
                        Date d'échéance
                    </label>

                    <input
                        type="date"
                        id="date_echeance"
                        name="date_echeance"
                        value="<?php echo $dateEcheance; ?>"
                    >

                </div>

                <div>

                    <label for="duree_estimee">
                        Durée estimée
                    </label>

                    <input
                        type="number"
                        id="duree_estimee"
                        name="duree_estimee"
                        value="<?php echo $dureeEstimee; ?>"
                        min="1"
                        placeholder="Ex. 30"
                    >

                    <p>
                        Durée en minutes.
                    </p>

                    <?php if ($erreurDuree !== ''): ?>

                        <p class="message">
                            <?php echo $erreurDuree; ?>
                        </p>

                    <?php endif; ?>

                </div>

                <div>

                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer la tâche
                    </button>

                    <a
                        href="/TaskFlow/Web/taches.php"
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