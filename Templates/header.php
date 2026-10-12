<?php

if (session_id() === '') {
    session_start();
}

if (!isset($titrePage) || $titrePage === '') {
    $titrePage = 'TaskFlow';
}

$pageActuelle = basename(
    isset($_SERVER['SCRIPT_NAME'])
        ? $_SERVER['SCRIPT_NAME']
        : ''
);


/*
|--------------------------------------------------------------------------
| Détermine si un menu est actif
|--------------------------------------------------------------------------
*/
function taskflowMenuClasse($pages, $pageActuelle)
{
    if (in_array($pageActuelle, $pages)) {
        return 'navigation-lien actif';
    }

    return 'navigation-lien';
}


/*
|--------------------------------------------------------------------------
| aria-current pour le menu actif
|--------------------------------------------------------------------------
*/
function taskflowMenuCourant($pages, $pageActuelle)
{
    if (in_array($pageActuelle, $pages)) {
        return 'aria-current="page"';
    }

    return '';
}


/*
|--------------------------------------------------------------------------
| Informations utilisateur
|--------------------------------------------------------------------------
*/
$prenomUtilisateur = isset($_SESSION['utilisateur_prenom'])
    ? trim($_SESSION['utilisateur_prenom'])
    : '';

$nomUtilisateur = isset($_SESSION['utilisateur_nom'])
    ? trim($_SESSION['utilisateur_nom'])
    : '';

$nomAffiche = trim(
    $prenomUtilisateur . ' ' . $nomUtilisateur
);

if ($nomAffiche === '') {
    if (isset($_SESSION['utilisateur_nom_complet'])) {
        $nomAffiche = trim(
            $_SESSION['utilisateur_nom_complet']
        );
    }
}

if ($nomAffiche === '') {
    $nomAffiche = 'Mon compte';
}


/*
|--------------------------------------------------------------------------
| Création des initiales
|--------------------------------------------------------------------------
*/
$initiales = 'TF';

$morceauxNom = preg_split(
    '/\s+/',
    $nomAffiche
);

if (is_array($morceauxNom)) {

    $initiales = '';

    foreach ($morceauxNom as $morceau) {

        if ($morceau !== '') {

            $initiales .= strtoupper(
                substr($morceau, 0, 1)
            );
        }

        if (strlen($initiales) >= 2) {
            break;
        }
    }
}

if ($initiales === '') {
    $initiales = 'TF';
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

    <title>
        <?php
        echo htmlspecialchars(
            $titrePage,
            ENT_QUOTES,
            'UTF-8'
        );
        ?>
        - TaskFlow
    </title>

    <link
        rel="stylesheet"
        href="/TaskFlow/Assets/css/style.css"
    >

</head>

<body>

<div class="application">


    <!-- ======================================================
         BARRE LATERALE
         ====================================================== -->

    <aside class="barre-laterale">


        <!-- LOGO -->

        <a
            href="/TaskFlow/Web/index.php"
            class="logo"
        >

            <span class="logo-symbole">
                T
            </span>

            <span class="logo-texte">
                TaskFlow
            </span>

        </a>


        <!-- NAVIGATION -->

        <div class="navigation-contenu">

            <p class="navigation-label">
                MENU PRINCIPAL
            </p>


            <nav
                class="navigation navigation-laterale"
                aria-label="Navigation principale"
            >


                <!-- TABLEAU DE BORD -->

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

                    <span
                        class="navigation-icone"
                        aria-hidden="true"
                    >
                        ■
                    </span>

                    <span>
                        Tableau de bord
                    </span>

                </a>


                <!-- MES TACHES -->

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
                            'modifier-sous-tache.php',
                            'supprimer-sous-tache.php'
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
                            'modifier-sous-tache.php',
                            'supprimer-sous-tache.php'
                        ),
                        $pageActuelle
                    );
                    ?>
                >

                    <span
                        class="navigation-icone"
                        aria-hidden="true"
                    >
                        ☷
                    </span>

                    <span>
                        Mes tâches
                    </span>

                </a>


                <!-- CATEGORIES -->

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

                    <span
                        class="navigation-icone"
                        aria-hidden="true"
                    >
                        ▦
                    </span>

                    <span>
                        Catégories
                    </span>

                </a>


                <!-- RAPPELS -->

                <a
                    href="/TaskFlow/Web/rappels.php"
                    class="<?php
                    echo taskflowMenuClasse(
                        array('rappels.php'),
                        $pageActuelle
                    );
                    ?>"
                    <?php
                    echo taskflowMenuCourant(
                        array('rappels.php'),
                        $pageActuelle
                    );
                    ?>
                >

                    <span
                        class="navigation-icone"
                        aria-hidden="true"
                    >
                        ◷
                    </span>

                    <span>
                        Rappels
                    </span>

                </a>


                <!-- HISTORIQUE -->

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

                    <span
                        class="navigation-icone"
                        aria-hidden="true"
                    >
                        ↻
                    </span>

                    <span>
                        Historique
                    </span>

                </a>


            </nav>

        </div>


        <!-- ==================================================
             PROFIL EN BAS DE LA BARRE LATERALE
             ================================================== -->

        <div class="profil-lateral">


            <div class="profil-avatar">

                <?php
                echo htmlspecialchars(
                    $initiales,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>


            <div class="profil-informations">

                <p class="profil-nom">

                    <?php
                    echo htmlspecialchars(
                        $nomAffiche,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </p>

                <p class="profil-role">
                    Espace personnel
                </p>

            </div>


            <a
                href="/TaskFlow/Web/deconnexion.php"
                class="profil-deconnexion"
                title="Se déconnecter"
                aria-label="Se déconnecter"
            >
                ↪
            </a>

        </div>


    </aside>


    <!-- ======================================================
         ZONE PRINCIPALE
         ====================================================== -->

    <main class="zone-application">

        <div class="contenu-principal">