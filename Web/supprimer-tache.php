<?php

header('Content-Type: text/html; charset=UTF-8');

/*
 * 1. Chargement des classes nécessaires
 */
require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Entity/Tache.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/HistoriqueRepository.php';
require_once __DIR__ . '/../App/Service/HistoriqueService.php';

/*
 * 2. Vérification de la session
 */
Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

/*
 * 3. Vérification de l'identifiant de la tâche
 */
$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

/*
 * 4. Initialisation des repositories et du service
 */
$database = new Database();

$tacheRepository = new TacheRepository($database);
$historiqueRepository = new HistoriqueRepository($database);

$historiqueService = new HistoriqueService(
    $historiqueRepository
);

/*
 * 5. Recherche de la tâche
 */
$tache = $tacheRepository->trouverParId($id);

if (
    $tache === null
    || (int) $tache->getUtilisateurId() !== (int) $utilisateurId
) {
    header('Location: /TaskFlow/Web/taches.php');
    exit;
}

/*
 * 6. Enregistrement de la suppression dans l'historique
 */
$titreTache = $tache->getTitre();

$historiqueService->enregistrer(
    $utilisateurId,
    $id,
    'suppression',
    'La tâche "' . $titreTache . '" a été supprimée.'
);

/*
 * 7. Suppression de la tâche
 */
$tacheRepository->supprimer($id);

/*
 * 8. Redirection vers la liste des tâches
 */
header('Location: /TaskFlow/Web/taches.php');
exit;