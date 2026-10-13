<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';

require_once __DIR__ . '/../Entity/Tache.php';
require_once __DIR__ . '/../Entity/SousTache.php';

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/SousTacheRepository.php';

require_once __DIR__ . '/../Validation/SousTacheValidator.php';

/**
 * Contrôleur responsable de la gestion des sous-tâches.
 */
class SousTacheController extends Controller
{
    private $tacheRepository;
    private $sousTacheRepository;

    public function __construct(Database $database)
    {
        $this->tacheRepository = new TacheRepository($database);
        $this->sousTacheRepository = new SousTacheRepository($database);
    }

    /**
     * Vérifie la session utilisateur.
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
     * Vérifie que la tâche principale appartient à l'utilisateur.
     */
    private function obtenirTacheAutorisee($tacheId, $utilisateurId)
    {
        $tacheId = (int) $tacheId;

        if ($tacheId <= 0) {
            return null;
        }

        $tache = $this->tacheRepository->trouverParId($tacheId);

        if (
            $tache === null
            || (int) $tache->getUtilisateurId() !== (int) $utilisateurId
        ) {
            return null;
        }

        return $tache;
    }

    /**
     * Recherche une sous-tâche et vérifie son propriétaire.
     */
    private function obtenirSousTacheAutorisee($id, $utilisateurId)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        $sousTache = $this->sousTacheRepository->trouverParId($id);

        if ($sousTache === null) {
            return null;
        }

        $tache = $this->obtenirTacheAutorisee(
            $sousTache->getTacheId(),
            $utilisateurId
        );

        if ($tache === null) {
            return null;
        }

        return $sousTache;
    }

    /**
     * Affiche les sous-tâches et traite leur création.
     */
    public function index($tacheId)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();
        $tacheId = (int) $tacheId;

        $tache = $this->obtenirTacheAutorisee(
            $tacheId,
            $utilisateurId
        );

        if ($tache === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $titre = '';
        $statut = 'a_faire';
        $erreurTitre = '';
        $erreurStatut = '';
        $message = '';
        $erreurs = array();

        if ($this->requeteEstPost()) {
            $titre = trim((string) $this->obtenirPost('titre', ''));
            $statut = trim(
                (string) $this->obtenirPost('statut', 'a_faire')
            );

            $sousTache = new SousTache();
            $sousTache->setTacheId($tacheId);
            $sousTache->setTitre($titre);
            $sousTache->setStatut($statut);

            $validator = new SousTacheValidator();

            if ($validator->valider($sousTache)) {
                try {
                    $this->sousTacheRepository->ajouter($sousTache);

                    $this->rediriger(
                        '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
                    );
                } catch (PDOException $exception) {
                    $message = 'Impossible de créer la sous-tâche.';
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
            }
        }

        $sousTaches = $this->sousTacheRepository->trouverParTache(
            $tacheId
        );

        $nombreSousTaches = count($sousTaches);
        $nombreSousTachesTerminees = 0;

        foreach ($sousTaches as $sousTache) {
            if ($sousTache->getStatut() === 'terminee') {
                $nombreSousTachesTerminees++;
            }
        }

        $progression = 0;

        if ($nombreSousTaches > 0) {
            $progression = (int) round(
                ($nombreSousTachesTerminees / $nombreSousTaches) * 100
            );
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/sous-taches.php',
            array(
                'titrePage' => 'Sous-tâches',
                'id' => $tacheId,
                'tache' => $tache,
                'titre' => $titre,
                'statut' => $statut,
                'erreurTitre' => $erreurTitre,
                'erreurStatut' => $erreurStatut,
                'message' => $message,
                'erreurs' => $erreurs,
                'sousTaches' => $sousTaches,
                'nombreSousTaches' => $nombreSousTaches,
                'nombreSousTachesTerminees' => $nombreSousTachesTerminees,
                'progression' => $progression
            )
        );
    }

    /**
     * Affiche et traite la modification d'une sous-tâche.
     */
    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $sousTache = $this->obtenirSousTacheAutorisee(
            $id,
            $utilisateurId
        );

        if ($sousTache === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $sousTache->getTacheId();

        $titre = $sousTache->getTitre();
        $statut = $sousTache->getStatut();

        $erreurTitre = '';
        $erreurStatut = '';
        $message = '';
        $erreurs = array();

        if ($this->requeteEstPost()) {
            $titre = trim((string) $this->obtenirPost('titre', ''));
            $statut = trim(
                (string) $this->obtenirPost('statut', 'a_faire')
            );

            $sousTache->setTitre($titre);
            $sousTache->setStatut($statut);

            $validator = new SousTacheValidator();

            if ($validator->valider($sousTache)) {
                try {
                    $this->sousTacheRepository->modifier($sousTache);

                    $this->rediriger(
                        '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
                    );
                } catch (PDOException $exception) {
                    $message = 'Impossible de modifier la sous-tâche.';
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
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/modifier-sous-tache.php',
            array(
                'titrePage' => 'Modifier une sous-tâche',
                'id' => (int) $id,
                'tacheId' => $tacheId,
                'sousTache' => $sousTache,
                'titre' => $titre,
                'statut' => $statut,
                'erreurTitre' => $erreurTitre,
                'erreurStatut' => $erreurStatut,
                'message' => $message,
                'erreurs' => $erreurs
            )
        );
    }

    /**
     * Supprime une sous-tâche appartenant à l'utilisateur.
     */
    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $sousTache = $this->obtenirSousTacheAutorisee(
            $id,
            $utilisateurId
        );

        if ($sousTache === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $sousTache->getTacheId();

        try {
            $this->sousTacheRepository->supprimer(
                $sousTache->getId()
            );
        } catch (PDOException $exception) {
            $this->rediriger(
                '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
            );
        }

        $this->rediriger(
            '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
        );
    }
}