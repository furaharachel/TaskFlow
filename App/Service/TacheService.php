<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Tache.php';

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/CategorieRepository.php';
require_once __DIR__ . '/../Repository/HistoriqueRepository.php';

require_once __DIR__ . '/../Validation/TacheValidator.php';
require_once __DIR__ . '/HistoriqueService.php';

/**
 * Service applicatif des tâches.
 *
 * Coordonne les règles de validation, la persistance
 * et l'enregistrement des actions dans l'historique.
 */
class TacheService
{
    private $database;
    private $tacheRepository;
    private $categorieRepository;
    private $validator;
    private $historiqueService;

    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->tacheRepository = new TacheRepository($database);
        $this->categorieRepository = new CategorieRepository($database);
        $this->validator = new TacheValidator();

        $historiqueRepository = new HistoriqueRepository($database);

        $this->historiqueService = new HistoriqueService(
            $historiqueRepository
        );
    }

    /**
     * Liste les tâches et calcule les statistiques.
     */
    public function listerParUtilisateur($utilisateurId)
    {
        $taches = $this->tacheRepository->trouverParUtilisateur(
            $utilisateurId
        );

        $statistiques = array(
            'total' => count($taches),
            'a_faire' => 0,
            'en_cours' => 0,
            'terminees' => 0
        );

        foreach ($taches as $tache) {
            if ($tache->getStatut() === 'a_faire') {
                $statistiques['a_faire']++;
            } elseif ($tache->getStatut() === 'en_cours') {
                $statistiques['en_cours']++;
            } elseif ($tache->getStatut() === 'terminee') {
                $statistiques['terminees']++;
            }
        }

        return array(
            'taches' => $taches,
            'statistiques' => $statistiques
        );
    }

    /**
     * Recherche une tâche appartenant à l'utilisateur.
     */
    public function trouverPourUtilisateur($id, $utilisateurId)
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

    /**
     * Retourne les catégories disponibles pour les formulaires.
     */
    public function obtenirCategories()
    {
        return $this->categorieRepository->trouverTous();
    }

    /**
     * Crée une tâche après validation.
     */
    public function creer($utilisateurId, $donnees)
    {
        $tache = new Tache();

        $this->remplirTache($tache, $utilisateurId, $donnees);

        if (!$this->validator->valider($tache)) {
            return array(
                'succes' => false,
                'introuvable' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => 'Veuillez corriger les erreurs du formulaire.',
                'tache' => $tache
            );
        }

        $connexion = null;

        try {
            $connexion = $this->database->connect();
            $connexion->beginTransaction();

            $this->tacheRepository->ajouter($tache);

            $this->historiqueService->enregistrer(
                $utilisateurId,
                $tache->getId(),
                'creation',
                'La tâche "' . $tache->getTitre() . '" a été créée.'
            );

            $connexion->commit();

            return array(
                'succes' => true,
                'introuvable' => false,
                'erreurs' => array(),
                'message' => '',
                'tache' => $tache
            );

        } catch (Exception $exception) {
            if (
                $connexion !== null
                && $connexion->inTransaction()
            ) {
                $connexion->rollBack();
            }

            return array(
                'succes' => false,
                'introuvable' => false,
                'erreurs' => array(),
                'message' => 'Impossible de créer la tâche. Réessayez.',
                'tache' => $tache
            );
        }
    }

    /**
     * Modifie une tâche appartenant à l'utilisateur.
     */
    public function modifier($id, $utilisateurId, $donnees)
    {
        $tache = $this->trouverPourUtilisateur($id, $utilisateurId);

        if ($tache === null) {
            return array(
                'succes' => false,
                'introuvable' => true,
                'erreurs' => array(),
                'message' => 'Tâche introuvable.',
                'tache' => null
            );
        }

        $this->remplirTache($tache, $utilisateurId, $donnees);

        if (!$this->validator->valider($tache)) {
            return array(
                'succes' => false,
                'introuvable' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => 'Veuillez corriger les erreurs du formulaire.',
                'tache' => $tache
            );
        }

        try {
            $this->tacheRepository->modifier($tache);

            return array(
                'succes' => true,
                'introuvable' => false,
                'erreurs' => array(),
                'message' => '',
                'tache' => $tache
            );

        } catch (Exception $exception) {
            return array(
                'succes' => false,
                'introuvable' => false,
                'erreurs' => array(),
                'message' => 'Impossible d’enregistrer les modifications.',
                'tache' => $tache
            );
        }
    }

    /**
     * Supprime une tâche et enregistre son historique.
     */
    public function supprimer($id, $utilisateurId)
    {
        $tache = $this->trouverPourUtilisateur($id, $utilisateurId);

        if ($tache === null) {
            return array(
                'succes' => false,
                'message' => 'Tâche introuvable.'
            );
        }

        $connexion = null;

        try {
            $connexion = $this->database->connect();
            $connexion->beginTransaction();

            $this->historiqueService->enregistrer(
                $utilisateurId,
                $tache->getId(),
                'suppression',
                'La tâche "' . $tache->getTitre() . '" a été supprimée.'
            );

            $this->tacheRepository->supprimer($tache->getId());

            $connexion->commit();

            return array(
                'succes' => true,
                'message' => ''
            );

        } catch (Exception $exception) {
            if (
                $connexion !== null
                && $connexion->inTransaction()
            ) {
                $connexion->rollBack();
            }

            return array(
                'succes' => false,
                'message' => 'Impossible de supprimer la tâche.'
            );
        }
    }

    /**
     * Transfère les données reçues dans l'entité.
     */
    private function remplirTache(Tache $tache, $utilisateurId, $donnees)
    {
        $tache->setUtilisateurId($utilisateurId);

        $tache->setTitre($this->valeur($donnees, 'titre'));
        $tache->setDescription($this->valeur($donnees, 'description'));

        $categorieId = $this->valeur($donnees, 'categorie_id');

        $tache->setCategorieId(
            $categorieId !== ''
            && is_numeric($categorieId)
            && (int) $categorieId > 0
                ? (int) $categorieId
                : null
        );

        $tache->setStatut(
            $this->valeur($donnees, 'statut', 'a_faire')
        );

        $tache->setPriorite(
            $this->valeur($donnees, 'priorite', 'normale')
        );

        $dateEcheance = $this->valeur($donnees, 'date_echeance');

        $tache->setDateEcheance(
            $dateEcheance !== '' ? $dateEcheance : null
        );

        $dureeEstimee = $this->valeur($donnees, 'duree_estimee');

        $tache->setDureeEstimee(
            $dureeEstimee !== '' ? $dureeEstimee : null
        );
    }

    /**
     * Lit une valeur scalaire du formulaire.
     */
    private function valeur($donnees, $cle, $defaut = '')
    {
        if (
            !isset($donnees[$cle])
            || !is_scalar($donnees[$cle])
        ) {
            return $defaut;
        }

        return trim((string) $donnees[$cle]);
    }
}