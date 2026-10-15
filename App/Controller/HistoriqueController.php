<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Service/HistoriqueService.php';

class HistoriqueController extends Controller
{
    private $historiqueService;

    public function __construct(Database $database)
    {
        $this->historiqueService = new HistoriqueService($database);
    }

    public function index()
    {
        Session::demarrer();

        if (!Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/connexion.php');
        }

        $utilisateurId = (int) Session::obtenirUtilisateurId();

        try {
            $historiques = $this->historiqueService->listerParUtilisateur(
                $utilisateurId
            );
        } catch (PDOException $exception) {
            $historiques = array();
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/historique.php',
            array(
                'titrePage' => 'Historique',
                'historiques' => $historiques
            )
        );
    }
}