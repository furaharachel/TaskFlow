<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// 1. Vérification de sécurité : si l'utilisateur n'est pas connecté, redirection
if (!isset($_SESSION['utilisateur'])) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit();
}

$utilisateur = $_SESSION['utilisateur'];
$prenom = method_exists($utilisateur, 'getPrenom') ? $utilisateur->getPrenom() : '';
$userId = method_exists($utilisateur, 'getId') ? $utilisateur->getId() : null;

// 2. Inclusion des dépendances nécessaires
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Service/TacheService.php';

$database = new Database();
$tacheService = new TacheService($database);

$message = '';
$totalTaches = 0;
$tachesAFaire = 0;
$tachesEnCours = 0;
$tachesTerminees = 0;
$taches = array();

// 3. Récupération des données métiers via le Service
try {
    if ($userId !== null) {
        $totalTaches = $tacheService->compterTotalParUtilisateur($userId);
        $tachesAFaire = $tacheService->compterParStatutEtUtilisateur($userId, 'a_faire');
        $tachesEnCours = $tacheService->compterParStatutEtUtilisateur($userId, 'en_cours');
        $tachesTerminees = $tacheService->compterParStatutEtUtilisateur($userId, 'terminee');
        $taches = $tacheService->trouverRecentesParUtilisateur($userId);
    }
} catch (Exception $e) {
    $message = "Impossible de charger vos tâches pour le moment.";
}

// 4. Affichage du template du tableau de bord
require_once __DIR__ . '/../Templates/tableau-de-bord.php';