<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Service/TacheService.php';

/**
 * Contrôleur HTTP des tâches.
 */
class TacheController extends Controller
{
    private $tacheService;

    public function __construct(Database $database)
    {
        $this->tacheService = new TacheService($database);
    }

    private function obtenirUtilisateurConnecte()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        return (int) Session::obtenirUtilisateurId();
    }

    /**
     * Liste des tâches.
     */
    public function index()
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $resultat = $this->tacheService->listerParUtilisateur(
            $utilisateurId
        );

        $statistiques = $resultat['statistiques'];

        $this->afficherVue(
            __DIR__ . '/../../Templates/taches.php',
            array(
                'titrePage' => 'Mes tâches',
                'taches' => $resultat['taches'],
                'totalTaches' => $statistiques['total'],
                'nombreAFaire' => $statistiques['a_faire'],
                'nombreEnCours' => $statistiques['en_cours'],
                'nombreTerminees' => $statistiques['terminees']
            )
        );
    }

    /**
     * Formulaire de création.
     */
    public function creer()
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $valeurs = $this->valeursInitiales();
        $erreurs = array();
        $message = '';

        $categories = $this->chargerCategories($message);

        if ($this->requeteEstPost()) {
            $valeurs = $this->lireValeursPost($valeurs);

            $resultat = $this->tacheService->creer(
                $utilisateurId,
                $_POST
            );

            if ($resultat['succes']) {
                $this->rediriger('/TaskFlow/Web/taches.php');
            }

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];
        }

        $this->afficherFormulaire(
            'creer',
            0,
            true,
            $valeurs,
            $categories,
            $erreurs,
            $message
        );
    }

    /**
     * Formulaire de modification.
     */
    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();
        $id = (int) $id;

        $tache = $this->tacheService->trouverPourUtilisateur(
            $id,
            $utilisateurId
        );

        if ($tache === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $valeurs = $this->valeursDepuisTache($tache);
        $erreurs = array();
        $message = '';

        $categories = $this->chargerCategories($message);

        if ($this->requeteEstPost()) {
            $valeurs = $this->lireValeursPost($valeurs);

            $resultat = $this->tacheService->modifier(
                $id,
                $utilisateurId,
                $_POST
            );

            if ($resultat['introuvable']) {
                $this->rediriger('/TaskFlow/Web/taches.php');
            }

            if ($resultat['succes']) {
                $this->rediriger('/TaskFlow/Web/taches.php');
            }

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];
        }

        $this->afficherFormulaire(
            'modifier',
            $id,
            true,
            $valeurs,
            $categories,
            $erreurs,
            $message
        );
    }

    /**
     * Suppression d'une tâche.
     */
    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $resultat = $this->tacheService->supprimer(
            (int) $id,
            $utilisateurId
        );

        $this->rediriger('/TaskFlow/Web/taches.php');
    }

    private function valeursInitiales()
    {
        return array(
            'titre' => '',
            'description' => '',
            'categorieId' => '',
            'statut' => 'a_faire',
            'priorite' => 'normale',
            'dateEcheance' => '',
            'dureeEstimee' => ''
        );
    }

    private function valeursDepuisTache($tache)
    {
        return array(
            'titre' => $tache->getTitre(),
            'description' => $tache->getDescription(),
            'categorieId' => $tache->getCategorieId() === null
                ? ''
                : (string) $tache->getCategorieId(),
            'statut' => $tache->getStatut(),
            'priorite' => $tache->getPriorite(),
            'dateEcheance' => $tache->getDateEcheance() === null
                ? ''
                : $tache->getDateEcheance(),
            'dureeEstimee' => $tache->getDureeEstimee() === null
                ? ''
                : (string) $tache->getDureeEstimee()
        );
    }

    private function lireValeursPost($valeurs)
    {
        $correspondance = array(
            'titre' => 'titre',
            'description' => 'description',
            'categorie_id' => 'categorieId',
            'statut' => 'statut',
            'priorite' => 'priorite',
            'date_echeance' => 'dateEcheance',
            'duree_estimee' => 'dureeEstimee'
        );

        foreach ($correspondance as $champ => $cle) {
            if (
                isset($_POST[$champ])
                && is_scalar($_POST[$champ])
            ) {
                $valeurs[$cle] = trim((string) $_POST[$champ]);
            }
        }

        return $valeurs;
    }

    private function chargerCategories(&$message)
    {
        try {
            return $this->tacheService->obtenirCategories();
        } catch (Exception $exception) {
            $message = 'Impossible de charger les catégories.';
            return array();
        }
    }

    private function afficherFormulaire(
        $mode,
        $id,
        $tacheExiste,
        $valeurs,
        $categories,
        $erreurs,
        $message
    ) {
        $donnees = array_merge(
            $valeurs,
            array(
                'titrePage' => $mode === 'creer'
                    ? 'Nouvelle tâche'
                    : 'Modifier une tâche',
                'id' => (int) $id,
                'tacheExiste' => $tacheExiste,
                'categories' => $categories,
                'erreurs' => $erreurs,
                'message' => $message,
                'erreurTitre' => isset($erreurs['titre'])
                    ? $erreurs['titre']
                    : '',
                'erreurStatut' => isset($erreurs['statut'])
                    ? $erreurs['statut']
                    : '',
                'erreurPriorite' => isset($erreurs['priorite'])
                    ? $erreurs['priorite']
                    : '',
                'erreurDuree' => isset($erreurs['duree_estimee'])
                    ? $erreurs['duree_estimee']
                    : ''
            )
        );

        $cheminVue = $mode === 'creer'
            ? __DIR__ . '/../../Templates/creer-tache.php'
            : __DIR__ . '/../../Templates/modifier-tache.php';

        $this->afficherVue($cheminVue, $donnees);
    }
}