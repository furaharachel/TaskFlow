<?php

require_once __DIR__ . '/../App/Session.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../App/TacheValidator.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

$database = new Database();

$tacheRepository = new TacheRepository($database);
$categorieRepository = new CategorieRepository($database);

$utilisateurId = Session::obtenirUtilisateurId();

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

$tacheExiste = false;

$message = '';

$categories = array();
$erreurs = array();

$titre = '';
$description = '';
$categorieId = '';
$statut = '';
$priorite = '';
$dateEcheance = '';
$dureeEstimee = '';

$erreurTitre = '';
$erreurStatut = '';
$erreurPriorite = '';
$erreurDuree = '';

if ($id <= 0) {

    $message = 'Tâche introuvable.';

} else {

    try {

        $tache = $tacheRepository->trouverParId($id);

        if (
            $tache === null ||
            (int) $tache->getUtilisateurId() !== $utilisateurId
        ) {

            $message =
                'Cette tâche n’existe pas ou ne vous appartient pas.';

        } else {

            $tacheExiste = true;

            $categories =
                $categorieRepository->trouverTous();

            $titre = $tache->getTitre();
            $description = $tache->getDescription();

            if ($tache->getCategorieId() === null) {
                $categorieId = '';
            } else {
                $categorieId = (string) $tache->getCategorieId();
            }

            $statut = $tache->getStatut();
            $priorite = $tache->getPriorite();

            if ($tache->getDateEcheance() === null) {
                $dateEcheance = '';
            } else {
                $dateEcheance = $tache->getDateEcheance();
            }

            if ($tache->getDureeEstimee() === null) {
                $dureeEstimee = '';
            } else {
                $dureeEstimee =
                    (string) $tache->getDureeEstimee();
            }
        }

    } catch (PDOException $exception) {

        $message =
            'Impossible de charger la tâche.';
    }
}

if (
    $tacheExiste &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    $titre = isset($_POST['titre'])
        ? trim($_POST['titre'])
        : '';

    $description = isset($_POST['description'])
        ? trim($_POST['description'])
        : '';

    $categorieId = isset($_POST['categorie_id'])
        ? trim($_POST['categorie_id'])
        : '';

    $statut = isset($_POST['statut'])
        ? trim($_POST['statut'])
        : '';

    $priorite = isset($_POST['priorite'])
        ? trim($_POST['priorite'])
        : '';

    $dateEcheance = isset($_POST['date_echeance'])
        ? trim($_POST['date_echeance'])
        : '';

    $dureeEstimee = isset($_POST['duree_estimee'])
        ? trim($_POST['duree_estimee'])
        : '';

    $tache->setUtilisateurId($utilisateurId);

    if ($categorieId === '') {
        $tache->setCategorieId(null);
    } else {
        $tache->setCategorieId((int) $categorieId);
    }

    $tache->setTitre($titre);
    $tache->setDescription($description);
    $tache->setStatut($statut);
    $tache->setPriorite($priorite);

    if ($dateEcheance === '') {
        $tache->setDateEcheance(null);
    } else {
        $tache->setDateEcheance($dateEcheance);
    }

    if ($dureeEstimee === '') {
        $tache->setDureeEstimee(null);
    } else {
        $tache->setDureeEstimee((int) $dureeEstimee);
    }

    $validator = new TacheValidator();

    if ($validator->valider($tache)) {

        try {

            $tacheRepository->modifier($tache);

            header('Location: /TaskFlow/Web/taches.php');
            exit;

        } catch (PDOException $exception) {

            $message =
                'Impossible d’enregistrer les modifications.';
        }

    } else {

        $erreurs = $validator->getErreurs();

        if (isset($erreurs['titre'])) {
            $erreurTitre = $erreurs['titre'];
        }

        if (isset($erreurs['statut'])) {
            $erreurStatut = $erreurs['statut'];
        }

        if (isset($erreurs['priorite'])) {
            $erreurPriorite = $erreurs['priorite'];
        }

        if (isset($erreurs['duree_estimee'])) {
            $erreurDuree = $erreurs['duree_estimee'];
        }
    }
}

require_once __DIR__ . '/../Templates/modifier-tache.php';