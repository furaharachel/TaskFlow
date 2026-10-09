<?php

if (!isset($titrePage) || $titrePage === '') {
    $titrePage = 'TaskFlow';
}

$pageActuelle = basename(
    isset($_SERVER['SCRIPT_NAME'])
        ? $_SERVER['SCRIPT_NAME']
        : ''
);

function taskflowMenuClasse($pages, $pageActuelle)
{
    if (in_array($pageActuelle, $pages)) {
        return 'actif';
    }

    return '';
}

function taskflowMenuCourant($pages, $pageActuelle)
{
    if (in_array($pageActuelle, $pages)) {
        return 'aria-current="page"';
    }

    return '';
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

    <title><?php
        echo htmlspecialchars(
            $titrePage,
            ENT_QUOTES,
            'UTF-8'
        );
    ?> — TaskFlow</title>

    <link
        rel="stylesheet"
        type="text/css"
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

        <nav
            class="navigation"
            aria-label="Navigation principale"
        >

            <a
                href="/TaskFlow/Web/index.php"
                class="<?php
                    echo taskflowMenuClasse(
                        array('index.php'),
                        $pageActuelle
                    );
                ?>"
                <?php
                    echo taskflowMenuCourant(
                        array('index.php'),
                        $pageActuelle
                    );
                ?>
            >
                Accueil
            </a>

            <a
                href="/TaskFlow/Web/taches.php"
                class="<?php
                    echo taskflowMenuClasse(
                        array(
                            'taches.php',
                            'creer-tache.php',
                            'modifier-tache.php',
                            'supprimer-tache.php',
                            'sous-taches.php',
                            'rappels.php'
                        ),
                        $pageActuelle
                    );
                ?>"
                <?php
                    echo taskflowMenuCourant(
                        array(
                            'taches.php',
                            'creer-tache.php',
                            'modifier-tache.php',
                            'supprimer-tache.php',
                            'sous-taches.php',
                            'rappels.php'
                        ),
                        $pageActuelle
                    );
                ?>
            >
                Mes tâches
            </a>

            <a
                href="/TaskFlow/Web/categories.php"
                class="<?php
                    echo taskflowMenuClasse(
                        array(
                            'categories.php',
                            'creer-categorie.php',
                            'modifier-categorie.php',
                            'supprimer-categorie.php'
                        ),
                        $pageActuelle
                    );
                ?>"
                <?php
                    echo taskflowMenuCourant(
                        array(
                            'categories.php',
                            'creer-categorie.php',
                            'modifier-categorie.php',
                            'supprimer-categorie.php'
                        ),
                        $pageActuelle
                    );
                ?>
            >
                Catégories
            </a>

            <a
                href="/TaskFlow/Web/historique.php"
                class="<?php
                    echo taskflowMenuClasse(
                        array('historique.php'),
                        $pageActuelle
                    );
                ?>"
                <?php
                    echo taskflowMenuCourant(
                        array('historique.php'),
                        $pageActuelle
                    );
                ?>
            >
                Historique
            </a>

        </nav>

    </div>
</header>