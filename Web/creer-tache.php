<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../App/Session.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Tache.php';
require_once __DIR__ . '/../App/TacheValidator.php';
require_once __DIR__ . '/../App/Repository/TacheRepository.php';
require_once __DIR__ . '/../App/Repository/HistoriqueRepository.php';
require_once __DIR__ . '/../App/HistoriqueService.php';
require_once __DIR__ . '/../App/Categorie.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

Session::demarrer();

if (!Session::estConnecte()) {
    header('Location: /TaskFlow/Web/connexion.php');
    exit;
}

$utilisateurId = Session::obtenirUtilisateurId();

$erreur = '';
$message = '';

$titre = '';
$description = '';
$categorieId = 0;
$statut = 'a_faire';
$priorite = 'normale';
$dateEcheance = '';
$dureeEstimee = '';

$erreurTitre = '';
$erreurStatut = '';
$erreurPriorite = '';
$erreurDuree = '';

$categories = array();
$erreurs = array();

$database = new Database();

$tacheRepository = new TacheRepository($database);
$historiqueRepository = new HistoriqueRepository($database);
$historiqueService = new HistoriqueService($historiqueRepository);
$categorieRepository = new CategorieRepository($database);

try {
    $categories = $categorieRepository->trouverTous();
} catch (PDOException $exception) {
    $erreur = 'Impossible de charger les catégories.';
}

$validator = new TacheValidator();
$tache = new Tache();

$tache->setUtilisateurId($utilisateurId);
$tache->setCategorieId(null);
$tache->setTitre('');
$tache->setDescription('');
$tache->setStatut('a_faire');
$tache->setPriorite('normale');
$tache->setDateEcheance(null);
$tache->setDureeEstimee(null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = isset($_POST['titre'])
        ? trim($_POST['titre'])
        : '';

    $description = isset($_POST['description'])
        ? trim($_POST['description'])
        : '';

    $categorieId = isset($_POST['categorie_id'])
        ? (int) $_POST['categorie_id']
        : 0;

    $statut = isset($_POST['statut'])
        ? $_POST['statut']
        : 'a_faire';

    $priorite = isset($_POST['priorite'])
        ? $_POST['priorite']
        : 'normale';

    $dateEcheance = isset($_POST['date_echeance'])
        ? trim($_POST['date_echeance'])
        : '';

    $dureeEstimee = isset($_POST['duree_estimee'])
        ? trim($_POST['duree_estimee'])
        : '';

    $tache->setTitre($titre);
    $tache->setDescription($description);
    $tache->setCategorieId(
        $categorieId > 0 ? $categorieId : null
    );
    $tache->setStatut($statut);
    $tache->setPriorite($priorite);
    $tache->setDateEcheance(
        $dateEcheance !== '' ? $dateEcheance : null
    );
    $tache->setDureeEstimee(
        $dureeEstimee !== '' ? $dureeEstimee : null
    );

    if ($validator->valider($tache)) {

        try {

            $tacheRepository->ajouter($tache);

            $historiqueService->enregistrer(
                $utilisateurId,
                $tache->getId(),
                'creation',
                'La tâche "' . $tache->getTitre()
                    . '" a été créée.'
            );

            header('Location: /TaskFlow/Web/taches.php');
            exit;

        } catch (PDOException $exception) {

            $message = 'Impossible de créer la tâche. Réessayez.';
        }

    } else {

        $erreurs = $validator->getErreurs();

        $message = 'Veuillez corriger les erreurs du formulaire.';

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