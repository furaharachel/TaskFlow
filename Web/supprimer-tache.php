<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/HistoriqueRepository.php';
require_once __DIR__ . '/../App/HistoriqueService.php';

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
$historiqueRepository = new HistoriqueRepository($database);
$historiqueService = new HistoriqueService($historiqueRepository);

$tache = $tacheRepository->trouverParId($id);

if ($tache === null) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

if ((int) $tache->getUtilisateurId() !== $utilisateurId) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

$titreTache = $tache->getTitre();

$historiqueService->enregistrer(
    $utilisateurId,
    $id,
    'suppression',
    'La tâche "' . $titreTache . '" a été supprimée.'
);

$tacheRepository->supprimer($id);

header('Location: /TaskFlow/Web/taches.php');
exit;