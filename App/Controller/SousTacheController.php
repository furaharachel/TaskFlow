<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../Service/SousTacheService.php';
require_once __DIR__ . '/../../Config/Database.php';

class SousTacheController extends Controller
{
    private $sousTacheService;

    public function __construct(Database $database)
    {
        $this->sousTacheService = new SousTacheService($database);
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

        $contexte = $this->sousTacheService->obtenirListePourTache(
            $tacheId,
            $utilisateurId
        );

        if ($contexte === null) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $tacheId = (int) $tacheId;
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

            $resultat = $this->sousTacheService->creer(
                $tacheId,
                $utilisateurId,
                $titre,
                $statut
            );

            if ($resultat['succes']) {
                $this->rediriger(
                    '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
                );
            }

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];

            if (isset($erreurs['titre'])) {
                $erreurTitre = $erreurs['titre'];
            }

            if (isset($erreurs['statut'])) {
                $erreurStatut = $erreurs['statut'];
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/sous-taches.php',
            array(
                'titrePage' => 'Sous-tâches',
                'id' => $tacheId,
                'tache' => $contexte['tache'],
                'titre' => $titre,
                'statut' => $statut,
                'erreurTitre' => $erreurTitre,
                'erreurStatut' => $erreurStatut,
                'message' => $message,
                'erreurs' => $erreurs,
                'sousTaches' => $contexte['sousTaches'],
                'nombreSousTaches' => $contexte['nombreSousTaches'],
                'nombreSousTachesTerminees' =>
                    $contexte['nombreSousTachesTerminees'],
                'progression' => $contexte['progression']
            )
        );
    }

    public function modifier($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $sousTache = $this->sousTacheService->obtenirPourModification(
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

            $resultat = $this->sousTacheService->modifier(
                $id,
                $utilisateurId,
                $titre,
                $statut
            );

            if ($resultat['succes']) {
                $this->rediriger(
                    '/TaskFlow/Web/sous-taches.php?id=' . $tacheId
                );
            }

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];

            if (isset($erreurs['titre'])) {
                $erreurTitre = $erreurs['titre'];
            }

            if (isset($erreurs['statut'])) {
                $erreurStatut = $erreurs['statut'];
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

    public function supprimer($id)
    {
        $utilisateurId = $this->obtenirUtilisateurConnecte();

        $resultat = $this->sousTacheService->supprimer(
            $id,
            $utilisateurId
        );

        if (
            !$resultat['succes']
            || empty($resultat['tacheId'])
        ) {
            if (!empty($resultat['tacheId'])) {
                $this->rediriger(
                    '/TaskFlow/Web/sous-taches.php?id='
                    . (int) $resultat['tacheId']
                );
            }

            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $this->rediriger(
            '/TaskFlow/Web/sous-taches.php?id='
            . (int) $resultat['tacheId']
        );
    }
}