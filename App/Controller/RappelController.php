<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Service/RappelService.php';

class RappelController extends Controller
{
    private $rappelService;

    public function __construct(Database $database)
    {
        $this->rappelService = new RappelService($database);
    }

    private function obtenirUtilisateurConnecte()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        return (int) Session::obtenirUtilisateurId();
    }

    public function index($tacheId)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $contexte = $this->rappelService->obtenirListePourTache(
            $tacheId,
            $utilisateurId
        );

        if ($contexte === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $tacheId;
        $tache = $contexte['tache'];

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

            $resultat = $this->rappelService->creer(
                $tacheId,
                $utilisateurId,
                $dateRappel,
                $messageRappel
            );

            if ($resultat['succes']) {
                $this->rediriger(
                    '/TaskFlow/Web/rappels.php?id=' . $tacheId
                );
            }

            $erreurs = $resultat['erreurs'];

            if (isset($erreurs['date_rappel'])) {
                $erreurDateRappel = $erreurs['date_rappel'];
            }

            if (isset($erreurs['message'])) {
                $erreurMessageRappel = $erreurs['message'];
            } elseif ($resultat['message'] !== '') {
                $erreurMessageRappel = $resultat['message'];
            }
        }

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
                'rappels' => $contexte['rappels']
            )
        );
    }

    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $contexte = $this->rappelService->obtenirPourModification(
            $id,
            $utilisateurId
        );

        if ($contexte === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $rappel = $contexte['rappel'];
        $tache = $contexte['tache'];
        $tacheId = (int) $rappel->getTacheId();

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

            $resultat = $this->rappelService->modifier(
                $id,
                $utilisateurId,
                $dateRappel,
                $messageRappel,
                $statut
            );

            if ($resultat['succes']) {
                $this->rediriger(
                    '/TaskFlow/Web/rappels.php?id=' . $tacheId
                );
            }

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];

            if (!empty($erreurs) && $message === '') {
                $message = 'Veuillez corriger les erreurs du formulaire.';
            }

            if (isset($erreurs['date_rappel'])) {
                $erreurDateRappel = $erreurs['date_rappel'];
            }

            if (isset($erreurs['message'])) {
                $erreurMessageRappel = $erreurs['message'];
            }

            if (isset($erreurs['statut'])) {
                $erreurStatut = $erreurs['statut'];
            }

            if ($resultat['tacheId'] === null) {
                $this->rediriger('/TaskFlow/Web/taches.php');
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

    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $resultat = $this->rappelService->supprimer(
            $id,
            $utilisateurId
        );

        if (empty($resultat['tacheId'])) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $this->rediriger(
            '/TaskFlow/Web/rappels.php?id='
            . (int) $resultat['tacheId']
        );
    }

    private function formaterDatePourFormulaire($date)
    {
        if ($date === null || $date === '') {
            return '';
        }

        $date = substr((string) $date, 0, 16);

        return str_replace(' ', 'T', $date);
    }
}