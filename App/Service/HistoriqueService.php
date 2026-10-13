<?php

require_once __DIR__ . '/../Entity/Historique.php';
require_once __DIR__ . '/../Repository/HistoriqueRepository.php';

/**
 * Gère les opérations métier liées à l'historique.
 */
class HistoriqueService
{
    private $historiqueRepository;

    public function __construct(
        HistoriqueRepository $historiqueRepository
    ) {
        $this->historiqueRepository = $historiqueRepository;
    }

    /**
     * Enregistre une action dans l'historique.
     */
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