<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/SousTacheRepository.php';
require_once __DIR__ . '/../App/SousTacheValidator.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$database = new Database();

$tacheRepository = new TacheRepository($database);
$sousTacheRepository = new SousTacheRepository($database);
$validator = new SousTacheValidator();

$sousTache = $sousTacheRepository->trouverParId($id);

if ($sousTache === null) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$tache = $tacheRepository->trouverParId(
    $sousTache->getTacheId()
);

if ($tache === null ||
    (int) $tache->getUtilisateurId() !== $utilisateurId) {

    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$titre = $sousTache->getTitre();
$statut = $sousTache->getStatut();

$erreurTitre = '';
$erreurStatut = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = isset($_POST['titre'])
        ? trim($_POST['titre'])
        : '';

    $statut = isset($_POST['statut'])
        ? $_POST['statut']
        : 'a_faire';

    $sousTache->setTitre($titre);
    $sousTache->setStatut($statut);

    if ($validator->valider($sousTache)) {

        $sousTacheRepository->modifier($sousTache);

        header(
            'Location: /TaskFlow/Web/sous-taches.php?id='
            . (int) $sousTache->getTacheId()
        );
        exit;
    }

    $erreurs = $validator->getErreurs();

    if (isset($erreurs['titre'])) {
        $erreurTitre = $erreurs['titre'];
    }

    if (isset($erreurs['statut'])) {
        $erreurStatut = $erreurs['statut'];
    }
}

require_once __DIR__ . '/../Templates/modifier-sous-tache.php';