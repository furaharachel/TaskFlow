<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Repository/HistoriqueRepository.php';
require_once __DIR__ . '/../Entity/Historique.php';

class HistoriqueService
{
    private $historiqueRepository;

    public function __construct(Database $database)
    {
        $this->historiqueRepository = new HistoriqueRepository($database);
    }

    public function enregistrer(
        $utilisateurId,
        $tacheId,
        $action,
        $description
    ) {
        $historique = new Historique();

        $historique->setUtilisateurId($utilisateurId);
        $historique->setTacheId($tacheId);
        $historique->setAction($action);
        $historique->setDescription($description);

        return $this->historiqueRepository->ajouter($historique);
    }

    public function listerParUtilisateur($utilisateurId)
    {
        if (!is_numeric($utilisateurId) || (int) $utilisateurId <= 0) {
            return array();
        }

        return $this->historiqueRepository->trouverParUtilisateur(
            (int) $utilisateurId
        );
    }
}