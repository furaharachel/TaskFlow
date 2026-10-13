<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';

require_once __DIR__ . '/../Entity/Tache.php';
require_once __DIR__ . '/../Entity/Rappel.php';

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/RappelRepository.php';

require_once __DIR__ . '/../Validation/RappelValidator.php';

/**
 * Contrôleur de gestion des rappels.
 */
class RappelController extends Controller
{
    private $database;
    private $tacheRepository;
    private $rappelRepository;

    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->tacheRepository = new TacheRepository($database);
        $this->rappelRepository = new RappelRepository($database);
    }

    /*
     * AUTHENTIFICATION ET AUTORISATIONS
     */

    private function obtenirUtilisateurConnecte()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        return (int) Session::obtenirUtilisateurId();
    }

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

    private function obtenirRappelAutorise($id, $utilisateurId)
    {
        $rappel = $this->rappelRepository->trouverParId((int) $id);

        if ($rappel === null) {
            return null;
        }

        $tache = $this->obtenirTacheAutorisee(
            $rappel->getTacheId(),
            $utilisateurId
        );

        if ($tache === null) {
            return null;
        }

        return $rappel;
    }

    /*
     * LISTE ET CRÉATION
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

        $dateRappel = '';
        $messageRappel = '';
        $erreurDateRappel = '';
        $erreurMessageRappel = '';

        if ($this->requeteEstPost()) {
            $dateRappel = trim(
                (string) $this->obtenirPost('date_rappel', '')
            );

            $messageRappel = trim(
                (string) $this->obtenirPost('message', '')
            );

            $rappel = new Rappel();

            $rappel->setTacheId($tacheId);
            $rappel->setDateRappel(
                str_replace('T', ' ', $dateRappel)
            );
            $rappel->setMessage($messageRappel);
            $rappel->setStatut('actif');

            $validator = new RappelValidator();

            if ($validator->valider($rappel)) {
                try {
                    $this->rappelRepository->ajouter($rappel);

                    $this->rediriger(
                        '/TaskFlow/Web/rappels.php?id=' . $tacheId
                    );
                } catch (PDOException $exception) {
                    $erreurMessageRappel =
                        'Impossible de créer le rappel.';
                }
            } else {
                $erreurs = $validator->getErreurs();

                if (isset($erreurs['date_rappel'])) {
                    $erreurDateRappel = $erreurs['date_rappel'];
                }

                if (isset($erreurs['message'])) {
                    $erreurMessageRappel = $erreurs['message'];
                }
            }
        }

        $rappels = $this->rappelRepository->trouverParTache($tacheId);

        $this->afficherVue(
            __DIR__ . '/../../Templates/rappels.php',
            array(
                'titrePage' => 'Rappels',
                'tacheId' => $tacheId,
                'tache' => $tache,
                'dateRappel' => $dateRappel,
                'messageRappel' => $messageRappel,
                'erreurDateRappel' => $erreurDateRappel,
                'erreurMessageRappel' => $erreurMessageRappel,
                'rappels' => $rappels
            )
        );
    }

    /*
     * MODIFICATION
     */

    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $rappel = $this->obtenirRappelAutorise(
            (int) $id,
            $utilisateurId
        );

        if ($rappel === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $rappel->getTacheId();

        $tache = $this->obtenirTacheAutorisee(
            $tacheId,
            $utilisateurId
        );

        $dateRappel = $this->formaterDatePourFormulaire(
            $rappel->getDateRappel()
        );

        $messageRappel = $rappel->getMessage();
        $statut = $rappel->getStatut();

        $erreurs = array();
        $erreurDateRappel = '';
        $erreurMessageRappel = '';
        $erreurStatut = '';
        $message = '';

        if ($this->requeteEstPost()) {
            $dateRappel = trim(
                (string) $this->obtenirPost('date_rappel', '')
            );

            $messageRappel = trim(
                (string) $this->obtenirPost('message', '')
            );

            $statut = trim(
                (string) $this->obtenirPost('statut', '')
            );

            $rappel->setDateRappel(
                str_replace('T', ' ', $dateRappel)
            );

            $rappel->setMessage($messageRappel);
            $rappel->setStatut($statut);

            $validator = new RappelValidator();

            if ($validator->valider($rappel)) {
                try {
                    $this->rappelRepository->modifier($rappel);

                    $this->rediriger(
                        '/TaskFlow/Web/rappels.php?id=' . $tacheId
                    );
                } catch (PDOException $exception) {
                    $message =
                        'Impossible d’enregistrer les modifications.';
                }
            } else {
                $erreurs = $validator->getErreurs();

                $message = 'Veuillez corriger les erreurs du formulaire.';

                if (isset($erreurs['date_rappel'])) {
                    $erreurDateRappel = $erreurs['date_rappel'];
                }

                if (isset($erreurs['message'])) {
                    $erreurMessageRappel = $erreurs['message'];
                }

                if (isset($erreurs['statut'])) {
                    $erreurStatut = $erreurs['statut'];
                }
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/modifier-rappel.php',
            array(
                'titrePage' => 'Modifier un rappel',
                'id' => (int) $id,
                'tacheId' => $tacheId,
                'tache' => $tache,
                'dateRappel' => $dateRappel,
                'messageRappel' => $messageRappel,
                'statut' => $statut,
                'erreurs' => $erreurs,
                'erreurDateRappel' => $erreurDateRappel,
                'erreurMessageRappel' => $erreurMessageRappel,
                'erreurStatut' => $erreurStatut,
                'message' => $message
            )
        );
    }

    /*
     * SUPPRESSION
     */

    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $rappel = $this->obtenirRappelAutorise(
            (int) $id,
            $utilisateurId
        );

        if ($rappel === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $rappel->getTacheId();

        try {
            $this->rappelRepository->supprimer($rappel->getId());
        } catch (PDOException $exception) {
            $this->rediriger(
                '/TaskFlow/Web/rappels.php?id=' . $tacheId
            );
        }

        $this->rediriger(
            '/TaskFlow/Web/rappels.php?id=' . $tacheId
        );
    }

    /*
     * OUTILS PRIVÉS
     */

    private function formaterDatePourFormulaire($date)
    {
        if ($date === null || $date === '') {
            return '';
        }

        $date = substr((string) $date, 0, 16);

        return str_replace(' ', 'T', $date);
    }
}