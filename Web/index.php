<?php

session_start();

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$database = new Database();

$tacheRepository = new TacheRepository($database);

$utilisateurId = (int) $_SESSION['utilisateur_id'];

$prenom = isset($_SESSION['utilisateur_prenom'])
    ? $_SESSION['utilisateur_prenom']
    : '';

$nom = isset($_SESSION['utilisateur_nom'])
    ? $_SESSION['utilisateur_nom']
    : '';

$totalTaches = 0;
$tachesAFaire = 0;
$tachesEnCours = 0;
$tachesTerminees = 0;
$taches = array();

$message = '';

try {

    $totalTaches = $tacheRepository->compterParUtilisateur(
        $utilisateurId
    );

    $tachesAFaire = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'a_faire'
    );

    $tachesEnCours = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'en_cours'
    );

    $tachesTerminees = $tacheRepository->compterParUtilisateurEtStatut(
        $utilisateurId,
        'terminee'
    );

    $taches = $tacheRepository->trouverParUtilisateur(
        $utilisateurId
    );

} catch (PDOException $exception) {

    $message = 'Impossible de charger les données du tableau de bord.';
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

    <title>Tableau de bord — TaskFlow</title>

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

            <a
                href="/TaskFlow/Web/index.php"
                class="actif"
            >
                Tableau de bord
            </a>

            <a href="/TaskFlow/Web/taches.php">
                Mes tâches
            </a>

            <a href="/TaskFlow/Web/categories.php">
                Catégories
            </a>

        </nav>

    </div>

</header>

<main class="page">

    <div class="conteneur">

        <section class="introduction">

            <div>

                <p class="sur-titre">
                    Tableau de bord
                </p>

                <h1 class="titre-page">

                    Bonjour
                    <?php
                    echo htmlspecialchars(
                        $prenom . ' ' . $nom,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </h1>

                <p class="sous-titre">
                    Organisez vos tâches et avancez efficacement.
                </p>

            </div>

            <div>

                <a
                    href="/TaskFlow/Web/creer-tache.php"
                    class="bouton bouton-principal"
                >
                    Nouvelle tâche
                </a>

            </div>

        </section>

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

        <section class="statistiques">

            <article class="statistique">

                <p class="statistique-label">
                    Total
                </p>

                <p class="statistique-valeur">
                    <?php echo $totalTaches; ?>
                </p>

                <p class="statistique-description">
                    tâches
                </p>

            </article>

            <article class="statistique">

                <p class="statistique-label">
                    À faire
                </p>

                <p class="statistique-valeur">
                    <?php echo $tachesAFaire; ?>
                </p>

                <p class="statistique-description">
                    tâches restantes
                </p>

            </article>

            <article class="statistique">

                <p class="statistique-label">
                    En cours
                </p>

                <p class="statistique-valeur">
                    <?php echo $tachesEnCours; ?>
                </p>

                <p class="statistique-description">
                    tâches actives
                </p>

            </article>

            <article class="statistique">

                <p class="statistique-label">
                    Terminées
                </p>

                <p class="statistique-valeur">
                    <?php echo $tachesTerminees; ?>
                </p>

                <p class="statistique-description">
                    tâches réalisées
                </p>

            </article>

        </section>

        <section class="section">

            <div class="section-entete">

                <div>

                    <h2>
                        Mes tâches
                    </h2>

                    <p>
                        Les tâches actuellement enregistrées.
                    </p>

                </div>

                <a
                    href="/TaskFlow/Web/taches.php"
                    class="lien"
                >
                    Voir toutes les tâches
                </a>

            </div>

            <?php if (empty($taches)): ?>

                <div class="carte vide">

                    <h3>
                        Aucune tâche
                    </h3>

                    <p>
                        Commencez par créer votre première tâche.
                    </p>

                    <a
                        href="/TaskFlow/Web/creer-tache.php"
                        class="bouton bouton-principal"
                    >
                        Créer une tâche
                    </a>

                </div>

            <?php else: ?>

                <div class="liste-taches">

                    <?php foreach ($taches as $tache): ?>

                        <article class="tache">

                            <div class="tache-contenu">

                                <h3>

                                    <?php
                                    echo htmlspecialchars(
                                        $tache->getTitre(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </h3>

                                <?php if ($tache->getDescription() !== ''): ?>

                                    <p>

                                        <?php
                                        echo htmlspecialchars(
                                            $tache->getDescription(),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </p>

                                <?php endif; ?>

                                <div class="tache-informations">

                                    <span>

                                        Catégorie :
                                        <?php
                                        echo htmlspecialchars(
                                            $tache->getCategorieNom() !== null
                                                ? $tache->getCategorieNom()
                                                : 'Sans catégorie',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </span>

                                    <span>

                                        <?php

                                        if ($tache->getStatut() === 'a_faire') {
                                            echo 'À faire';
                                        } elseif ($tache->getStatut() === 'en_cours') {
                                            echo 'En cours';
                                        } elseif ($tache->getStatut() === 'terminee') {
                                            echo 'Terminée';
                                        }

                                        ?>

                                    </span>

                                    <span>

                                        <?php

                                        if ($tache->getPriorite() === 'basse') {
                                            echo 'Priorité basse';
                                        } elseif ($tache->getPriorite() === 'normale') {
                                            echo 'Priorité normale';
                                        } elseif ($tache->getPriorite() === 'haute') {
                                            echo 'Priorité haute';
                                        } elseif ($tache->getPriorite() === 'urgente') {
                                            echo 'Priorité urgente';
                                        }

                                        ?>

                                    </span>

                                    <?php if ($tache->getDateEcheance() !== null): ?>

                                        <span>

                                            Échéance :
                                            <?php
                                            echo htmlspecialchars(
                                                $tache->getDateEcheance(),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="tache-actions">

                                <a
                                    href="/TaskFlow/Web/modifier-tache.php?id=<?php echo (int) $tache->getId(); ?>"
                                    class="bouton bouton-secondaire"
                                >
                                    Modifier
                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

</body>

</html>