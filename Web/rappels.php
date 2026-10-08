<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../App/Rappel.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/RappelRepository.php';
require_once __DIR__ . '/../App/RappelValidator.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

$tacheId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($tacheId <= 0) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$database = new Database();

$tacheRepository = new TacheRepository($database);
$rappelRepository = new RappelRepository($database);
$validator = new RappelValidator();

$tache = $tacheRepository->trouverParId($tacheId);

if (
    $tache === null ||
    (int) $tache->getUtilisateurId() !== $utilisateurId
) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$dateRappel = '';
$messageRappel = '';

$erreurDateRappel = '';
$erreurMessageRappel = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dateRappel = isset($_POST['date_rappel'])
        ? trim($_POST['date_rappel'])
        : '';

    $messageRappel = isset($_POST['message'])
        ? trim($_POST['message'])
        : '';

    /*
     * Le navigateur envoie datetime-local
     * sous la forme : 2026-10-08T14:30
     *
     * Notre classe Rappel utilise :
     * 2026-10-08 14:30
     */
    if ($dateRappel !== '') {
        $dateRappel = str_replace('T', ' ', $dateRappel);
    }

    $rappel = new Rappel();

    $rappel->setTacheId($tacheId);
    $rappel->setDateRappel($dateRappel);
    $rappel->setMessage($messageRappel);
    $rappel->setStatut('actif');

    if ($validator->valider($rappel)) {

        $rappelRepository->ajouter($rappel);

        header(
            'Location: /TaskFlow/Web/rappels.php?id=' . $tacheId
        );

        exit;
    }

    $erreurs = $validator->getErreurs();

    if (isset($erreurs['date_rappel'])) {
        $erreurDateRappel = $erreurs['date_rappel'];
    }

    if (isset($erreurs['message'])) {
        $erreurMessageRappel = $erreurs['message'];
    }
}

$rappels = $rappelRepository->trouverParTache($tacheId);

require_once __DIR__ . '/../Templates/rappels.php';