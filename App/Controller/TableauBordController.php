<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Service/TacheService.php';

class TableauBordController extends Controller
{
    private $tacheService;

    public function __construct(Database $database)
    {
        $this->tacheService = new TacheService($database);
    }

    public function index()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        $utilisateurId = (int) Session::obtenirUtilisateurId();

        $prenom = isset($_SESSION['utilisateur_prenom'])
            && is_scalar($_SESSION['utilisateur_prenom'])
                ? $_SESSION['utilisateur_prenom']
                : '';

        $taches = array();

        $statistiques = array(
            'total' => 0,
            'a_faire' => 0,
            'en_cours' => 0,
            'terminees' => 0
        );

        $message = '';

        try {
            $resultat = $this->tacheService->listerParUtilisateur(
                $utilisateurId
            );

            $taches = $resultat['taches'];
            $statistiques = $resultat['statistiques'];

        } catch (PDOException $exception) {
            $message = 'Impossible de charger les données du tableau de bord.';
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/tableau-de-bord.php',
            array(
                'titrePage' => 'Tableau de bord',
                'prenom' => $prenom,
                'taches' => $taches,
                'totalTaches' => $statistiques['total'],
                'tachesAFaire' => $statistiques['a_faire'],
                'tachesEnCours' => $statistiques['en_cours'],
                'tachesTerminees' => $statistiques['terminees'],
                'message' => $message
            )
        );
    }
}