<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';

require_once __DIR__ . '/../Entity/Tache.php';

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/CategorieRepository.php';
require_once __DIR__ . '/../Repository/HistoriqueRepository.php';

require_once __DIR__ . '/../Validation/TacheValidator.php';
require_once __DIR__ . '/../Service/HistoriqueService.php';

/**
 * Contrôleur principal de la gestion des tâches.
 *
 * Responsabilités :
 * - afficher la liste des tâches et les statistiques ;
 * - créer une tâche ;
 * - modifier une tâche ;
 * - supprimer une tâche ;
 * - contrôler l'accès aux tâches ;
 * - transmettre les données aux vues.
 *
 * Les requêtes SQL restent dans les repositories.
 * Les règles de validation restent dans TacheValidator.
 */
class TacheController extends Controller
{
    private $database;
    private $tacheRepository;
    private $categorieRepository;
    private $historiqueService;

    /**
     * Initialise les dépendances du contrôleur.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->tacheRepository = new TacheRepository(
            $this->database
        );

        $this->categorieRepository = new CategorieRepository(
            $this->database
        );

        $historiqueRepository = new HistoriqueRepository(
            $this->database
        );

        $this->historiqueService = new HistoriqueService(
            $historiqueRepository
        );
    }

    /*
     * =========================================================
     * AUTHENTIFICATION ET AUTORISATIONS
     * =========================================================
     */

    /**
     * Vérifie la session et retourne l'identifiant utilisateur.
     */
    private function obtenirUtilisateurConnecte()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        return (int) Session::obtenirUtilisateurId();
    }

    /**
     * Recherche une tâche appartenant à l'utilisateur connecté.
     *
     * Retourne null si la tâche n'existe pas ou appartient
     * à un autre utilisateur.
     */
    private function obtenirTacheAutorisee($id, $utilisateurId)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        $tache = $this->tacheRepository->trouverParId($id);

        if (
            $tache === null
            || (int) $tache->getUtilisateurId() !== (int) $utilisateurId
        ) {
            return null;
        }

        return $tache;
    }

    /*
     * =========================================================
     * AFFICHAGE DE LA LISTE ET STATISTIQUES
     * =========================================================
     */

    /**
     * Affiche les tâches de l'utilisateur connecté.
     */
    public function index()
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $taches = $this->tacheRepository->trouverParUtilisateur(
            $utilisateurId
        );

        $statistiques = $this->calculerStatistiques($taches);

        $this->afficherVue(
            __DIR__ . '/../../Templates/taches.php',
            array(
                'titrePage' => 'Mes tâches',
                'taches' => $taches,
                'totalTaches' => $statistiques['total'],
                'nombreAFaire' => $statistiques['a_faire'],
                'nombreEnCours' => $statistiques['en_cours'],
                'nombreTerminees' => $statistiques['terminees']
            )
        );
    }

    /**
     * Calcule les statistiques des tâches chargées.
     */
    private function calculerStatistiques($taches)
    {
        $statistiques = array(
            'total' => count($taches),
            'a_faire' => 0,
            'en_cours' => 0,
            'terminees' => 0
        );

        foreach ($taches as $tache) {
            $statut = $tache->getStatut();

            if ($statut === 'a_faire') {
                $statistiques['a_faire']++;
            } elseif ($statut === 'en_cours') {
                $statistiques['en_cours']++;
            } elseif ($statut === 'terminee') {
                $statistiques['terminees']++;
            }
        }

        return $statistiques;
    }

    /*
     * =========================================================
     * CRÉATION D'UNE TÂCHE
     * =========================================================
     */

    /**
     * Affiche et traite le formulaire de création.
     */
    public function creer()
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $valeurs = $this->valeursParDefaut();

        $erreurs = array();
        $message = '';

        $categories = $this->chargerCategories($message);

        if ($this->requeteEstPost()) {
            $valeurs = $this->lireDonneesFormulaire($valeurs);

            $tache = new Tache();

            $this->remplirTache(
                $tache,
                $utilisateurId,
                $valeurs
            );

            $validator = new TacheValidator();

            if ($validator->valider($tache)) {
                try {
                    $this->enregistrerCreation(
                        $tache,
                        $utilisateurId
                    );

                    $this->rediriger('/TaskFlow/Web/taches.php');

                } catch (Exception $exception) {
                    $message =
                        'Impossible de créer la tâche. Réessayez.';
                }
            } else {
                $erreurs = $validator->getErreurs();

                $message =
                    'Veuillez corriger les erreurs du formulaire.';
            }
        }

        $this->afficherFormulaire(
            'creer',
            0,
            false,
            $valeurs,
            $categories,
            $erreurs,
            $message
        );
    }

    /**
     * Enregistre la tâche et son historique dans une transaction.
     */
    private function enregistrerCreation(
        Tache $tache,
        $utilisateurId
    ) {
        $connection = $this->database->connect();

        $connection->beginTransaction();

        try {
            $this->tacheRepository->ajouter($tache);

            $this->historiqueService->enregistrer(
                $utilisateurId,
                $tache->getId(),
                'creation',
                'La tâche "' . $tache->getTitre()
                    . '" a été créée.'
            );

            $connection->commit();

        } catch (Exception $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    /*
     * =========================================================
     * MODIFICATION D'UNE TÂCHE
     * =========================================================
     */

    /**
     * Affiche et traite le formulaire de modification.
     */
    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $tache = $this->obtenirTacheAutorisee(
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
            $valeurs = $this->lireDonneesFormulaire($valeurs);

            $this->remplirTache(
                $tache,
                $utilisateurId,
                $valeurs
            );

            $validator = new TacheValidator();

            if ($validator->valider($tache)) {
                try {
                    $this->tacheRepository->modifier($tache);

                    $this->rediriger('/TaskFlow/Web/taches.php');

                } catch (Exception $exception) {
                    $message =
                        'Impossible d’enregistrer les modifications.';
                }
            } else {
                $erreurs = $validator->getErreurs();

                $message =
                    'Veuillez corriger les erreurs du formulaire.';
            }
        }

        $this->afficherFormulaire(
            'modifier',
            (int) $id,
            true,
            $valeurs,
            $categories,
            $erreurs,
            $message
        );
    }

    /*
     * =========================================================
     * SUPPRESSION D'UNE TÂCHE
     * =========================================================
     */

    /**
     * Supprime une tâche et enregistre son historique.
     */
    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $tache = $this->obtenirTacheAutorisee(
            $id,
            $utilisateurId
        );

        if ($tache === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $connection = $this->database->connect();

        $connection->beginTransaction();

        try {
            $this->historiqueService->enregistrer(
                $utilisateurId,
                $tache->getId(),
                'suppression',
                'La tâche "' . $tache->getTitre()
                    . '" a été supprimée.'
            );

            $this->tacheRepository->supprimer(
                $tache->getId()
            );

            $connection->commit();

        } catch (Exception $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }

        $this->rediriger('/TaskFlow/Web/taches.php');
    }

    /*
     * =========================================================
     * PRÉPARATION DES FORMULAIRES
     * =========================================================
     */

    /**
     * Retourne les valeurs initiales d'une nouvelle tâche.
     */
    private function valeursParDefaut()
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

    /**
     * Récupère les valeurs d'une tâche existante.
     */
    private function valeursDepuisTache(Tache $tache)
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

    /**
     * Lit les données simples envoyées par le formulaire.
     */
    private function lireDonneesFormulaire($valeurs)
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
                $valeurs[$cle] = trim(
                    (string) $_POST[$champ]
                );
            }
        }

        return $valeurs;
    }

    /**
     * Remplit une entité avec les valeurs du formulaire.
     */
    private function remplirTache(
        Tache $tache,
        $utilisateurId,
        $valeurs
    ) {
        $tache->setUtilisateurId($utilisateurId);
        $tache->setTitre($valeurs['titre']);
        $tache->setDescription($valeurs['description']);

        $categorieId = $valeurs['categorieId'];

        if (
            $categorieId !== ''
            && is_numeric($categorieId)
            && (int) $categorieId > 0
        ) {
            $tache->setCategorieId((int) $categorieId);
        } else {
            $tache->setCategorieId(null);
        }

        $tache->setStatut($valeurs['statut']);
        $tache->setPriorite($valeurs['priorite']);

        $tache->setDateEcheance(
            $valeurs['dateEcheance'] !== ''
                ? $valeurs['dateEcheance']
                : null
        );

        /*
         * La valeur reste une chaîne à ce stade afin que
         * TacheValidator puisse contrôler la durée saisie.
         */
        $tache->setDureeEstimee(
            $valeurs['dureeEstimee'] !== ''
                ? $valeurs['dureeEstimee']
                : null
        );
    }

    /**
     * Charge les catégories disponibles pour le formulaire.
     */
    private function chargerCategories(&$message)
    {
        try {
            return $this->categorieRepository->trouverTous();
        } catch (Exception $exception) {
            $message = 'Impossible de charger les catégories.';

            return array();
        }
    }

    /**
     * Prépare les variables et affiche la vue appropriée.
     */
    private function afficherFormulaire(
        $mode,
        $id,
        $tacheExiste,
        $valeurs,
        $categories,
        $erreurs,
        $message
    ) {
        $donneesErreurs = array(
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
        );

        $donneesPage = array(
            'titrePage' => $mode === 'creer'
                ? 'Nouvelle tâche'
                : 'Modifier une tâche',

            'id' => (int) $id,
            'tacheExiste' => $tacheExiste,
            'categories' => $categories,
            'erreurs' => $erreurs,
            'message' => $message
        );

        $donnees = array_merge(
            $valeurs,
            $donneesErreurs,
            $donneesPage
        );

        if ($mode === 'creer') {
            $cheminVue = __DIR__
                . '/../../Templates/creer-tache.php';
        } else {
            $cheminVue = __DIR__
                . '/../../Templates/modifier-tache.php';
        }

        $this->afficherVue($cheminVue, $donnees);
    }
}