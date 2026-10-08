<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/SousTacheRepository.php';

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

$tacheId = (int) $sousTache->getTacheId();

$sousTacheRepository->supprimer($id);

header(
    'Location: /TaskFlow/Web/sous-taches.php?id=' . $tacheId
);
exit;