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

$categories = array();
$erreurs = array();

$message = '';

$titre = '';
$description = '';
$categorieId = '';
$statut = 'a_faire';
$priorite = 'normale';
$dateEcheance = '';
$dureeEstimee = '';

$erreurTitre = '';
$erreurStatut = '';
$erreurPriorite = '';
$erreurDuree = '';

try {

    $categories = $categorieRepository->trouverTous();

} catch (PDOException $exception) {

    $message = 'Impossible de charger les catégories.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    $tache = new Tache();

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

            $tacheRepository->ajouter($tache);

            header('Location: /TaskFlow/Web/taches.php');
            exit;

        } catch (PDOException $exception) {

            $message =
                'Impossible d’enregistrer la tâche.';
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

require_once __DIR__ . '/../Templates/creer-tache.php';