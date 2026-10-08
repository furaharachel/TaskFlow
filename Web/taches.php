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

$database = new Database();

$tacheRepository = new TacheRepository($database);

$taches = $tacheRepository->trouverParUtilisateur($utilisateurId);

$template = dirname(__DIR__) . '/Templates/taches.php';

if (!file_exists($template)) {
    die(
        'Erreur : le fichier Templates/taches.php est introuvable.<br>'
        . 'Chemin recherché : '
        . htmlspecialchars($template, ENT_QUOTES, 'UTF-8')
    );
}

require_once $template;