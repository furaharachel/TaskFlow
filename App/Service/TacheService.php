<?php

require_once __DIR__ . '/../../Config/Database.php';

require_once __DIR__ . '/../Entity/Tache.php';

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/CategorieRepository.php';

require_once __DIR__ . '/../Validation/TacheValidator.php';
require_once __DIR__ . '/HistoriqueService.php';

/**
 * Service applicatif des tâches.
 *
 * Responsabilités :
 * - appliquer les règles métier ;
 * - valider les données des tâches ;
 * - vérifier les droits d'accès aux tâches ;
 * - coordonner les repositories ;
 * - gérer les transactions ;
 * - enregistrer les actions dans l'historique.
 *
 * Compatible avec PHP 5.5.12.
 */
class TacheService
{
    private $database;
    private $tacheRepository;
    private $categorieRepository;
    private $validator;
    private $historiqueService;

    /**
     * Initialise les dépendances du service.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->tacheRepository = new TacheRepository($database);

        $this->categorieRepository = new CategorieRepository(
            $database
        );

        $this->validator = new TacheValidator();

        $this->historiqueService = new HistoriqueService(
            $database
        );
    }

    /**
     * Retourne les tâches d'un utilisateur et leurs statistiques.
     */
    public function listerParUtilisateur($utilisateurId)
    {
        if (!$this->idValide($utilisateurId)) {
            return array(
                'taches' => array(),
                'statistiques' => array(
                    'total' => 0,
                    'a_faire' => 0,
                    'en_cours' => 0,
                    'terminees' => 0
                )
            );
        }

        $taches = $this->tacheRepository->trouverParUtilisateur(
            (int) $utilisateurId
        );

        $statistiques = array(
            'total' => count($taches),
            'a_faire' => 0,
            'en_cours' => 0,
            'terminees' => 0
        );

        foreach ($taches as $tache) {
            switch ($tache->getStatut()) {
                case 'a_faire':
                    $statistiques['a_faire']++;
                    break;

                case 'en_cours':
                    $statistiques['en_cours']++;
                    break;

                case 'terminee':
                    $statistiques['terminees']++;
                    break;
            }
        }

        return array(
            'taches' => $taches,
            'statistiques' => $statistiques
        );
    }

    /**
     * Recherche une tâche appartenant à un utilisateur.
     */
    public function trouverPourUtilisateur($id, $utilisateurId)
    {
        if (
            !$this->idValide($id)
            || !$this->idValide($utilisateurId)
        ) {
            return null;
        }

        $tache = $this->tacheRepository->trouverParId((int) $id);

        if ($tache === null) {
            return null;
        }

        if (
            (int) $tache->getUtilisateurId()
            !== (int) $utilisateurId
        ) {
            return null;
        }

        return $tache;
    }

    /**
     * Retourne les catégories disponibles.
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
        if (!$this->idValide($utilisateurId)) {
            return $this->resultatErreur(
                'Identifiant utilisateur invalide.'
            );
        }

        if (!is_array($donnees)) {
            $donnees = array();
        }

        $tache = new Tache();

        $this->remplirTache(
            $tache,
            (int) $utilisateurId,
            $donnees
        );

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
                (int) $utilisateurId,
                $tache->getId(),
                'creation',
                'La tâche "' . $tache->getTitre()
                . '" a été créée.'
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
            $this->annulerTransaction($connexion);

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
        $tache = $this->trouverPourUtilisateur(
            $id,
            $utilisateurId
        );

        if ($tache === null) {
            return array(
                'succes' => false,
                'introuvable' => true,
                'erreurs' => array(),
                'message' => 'Tâche introuvable.',
                'tache' => null
            );
        }

        if (!is_array($donnees)) {
            $donnees = array();
        }

        $this->remplirTache(
            $tache,
            (int) $utilisateurId,
            $donnees
        );

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

            $this->tacheRepository->modifier($tache);

            $this->historiqueService->enregistrer(
                (int) $utilisateurId,
                $tache->getId(),
                'modification',
                'La tâche "' . $tache->getTitre()
                . '" a été modifiée.'
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
            $this->annulerTransaction($connexion);

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
     * Supprime une tâche appartenant à l'utilisateur.
     */
    public function supprimer($id, $utilisateurId)
    {
        $tache = $this->trouverPourUtilisateur(
            $id,
            $utilisateurId
        );

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
                (int) $utilisateurId,
                $tache->getId(),
                'suppression',
                'La tâche "' . $tache->getTitre()
                . '" a été supprimée.'
            );

            $this->tacheRepository->supprimer(
                $tache->getId()
            );

            $connexion->commit();

            return array(
                'succes' => true,
                'message' => ''
            );

        } catch (Exception $exception) {
            $this->annulerTransaction($connexion);

            return array(
                'succes' => false,
                'message' => 'Impossible de supprimer la tâche.'
            );
        }
    }

    /**
     * Transfère les données du formulaire vers l'entité.
     */
    private function remplirTache(
        Tache $tache,
        $utilisateurId,
        $donnees
    ) {
        $tache->setUtilisateurId((int) $utilisateurId);

        $tache->setTitre(
            $this->valeur($donnees, 'titre')
        );

        $tache->setDescription(
            $this->valeur($donnees, 'description')
        );

        $categorieId = $this->valeur(
            $donnees,
            'categorie_id'
        );

        if ($this->idValide($categorieId)) {
            $tache->setCategorieId((int) $categorieId);
        } else {
            $tache->setCategorieId(null);
        }

        $tache->setStatut(
            $this->valeur(
                $donnees,
                'statut',
                'a_faire'
            )
        );

        $tache->setPriorite(
            $this->valeur(
                $donnees,
                'priorite',
                'normale'
            )
        );

        $dateEcheance = $this->valeur(
            $donnees,
            'date_echeance'
        );

        $tache->setDateEcheance(
            $dateEcheance !== '' ? $dateEcheance : null
        );

        $dureeEstimee = $this->valeur(
            $donnees,
            'duree_estimee'
        );

        $tache->setDureeEstimee(
            $dureeEstimee !== '' ? $dureeEstimee : null
        );
    }

    /**
     * Récupère une valeur scalaire du formulaire.
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

    /**
     * Vérifie qu'un identifiant est un entier strictement positif.
     */
    private function idValide($id)
    {
        if (!is_scalar($id)) {
            return false;
        }

        $id = (string) $id;

        return preg_match('/^[0-9]+$/', $id) === 1
            && (int) $id > 0;
    }

    /**
     * Annule une transaction ouverte lorsqu'une opération échoue.
     */
    private function annulerTransaction($connexion)
    {
        if (
            $connexion !== null
            && $connexion->inTransaction()
        ) {
            $connexion->rollBack();
        }
    }

    /**
     * Construit un résultat d'échec générique.
     */
    private function resultatErreur($message)
    {
        return array(
            'succes' => false,
            'introuvable' => false,
            'erreurs' => array(),
            'message' => $message,
            'tache' => null
        );
    }
}