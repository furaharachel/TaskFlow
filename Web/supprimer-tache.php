<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$database = new Database();

$tacheRepository = new TacheRepository($database);

$tache = $tacheRepository->trouverParId($id);

if ($tache === null) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

if ((int) $tache->getUtilisateurId() !== $utilisateurId) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$tacheRepository->supprimer($id);

header('Location: /TaskFlow/Web/taches.php');
exit;