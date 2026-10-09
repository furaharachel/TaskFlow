<?php

require_once __DIR__ . '/Historique.php';
require_once __DIR__ . '/Repository/HistoriqueRepository.php';

class HistoriqueService
{
    private $historiqueRepository;

    public function __construct(HistoriqueRepository $historiqueRepository)
    {
        $this->historiqueRepository = $historiqueRepository;
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
}