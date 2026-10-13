<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Repository/HistoriqueRepository.php';

/**
 * Contrôleur responsable de la consultation de l'historique.
 */
class HistoriqueController extends Controller
{
    private $historiqueRepository;

    public function __construct(Database $database)
    {
        $this->historiqueRepository = new HistoriqueRepository(
            $database
        );
    }

    /**
     * Affiche l'historique de l'utilisateur connecté.
     */
    public function index()
    {
        // Vérification de la session.
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        // Identification de l'utilisateur.
        $utilisateurId = (int) Session::obtenirUtilisateurId();

        // Chargement de son historique.
        $historiques = $this->historiqueRepository
            ->trouverParUtilisateur($utilisateurId);

        // Transmission des données à la vue.
        $this->afficherVue(
            __DIR__ . '/../../Templates/historique.php',
            array(
                'titrePage' => 'Historique',
                'historiques' => $historiques
            )
        );
    }
}