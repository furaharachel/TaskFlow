<?php

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/HistoriqueRepository.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

$database = new Database();

$historiqueRepository = new HistoriqueRepository($database);

$historiques = $historiqueRepository->trouverParUtilisateur(
    $utilisateurId
);

require_once __DIR__ . '/../Templates/historique.php';