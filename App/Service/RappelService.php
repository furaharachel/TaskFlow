<?php

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Rappel.php';
require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/RappelRepository.php';
require_once __DIR__ . '/../Validation/RappelValidator.php';

class RappelService
{
    private $tacheRepository;
    private $rappelRepository;
    private $validator;

    public function __construct(Database $database)
    {
        $this->tacheRepository = new TacheRepository($database);
        $this->rappelRepository = new RappelRepository($database);
        $this->validator = new RappelValidator();
    }

    public function obtenirListePourTache($tacheId, $utilisateurId)
    {
        $tache = $this->obtenirTacheAutorisee(
            $tacheId,
            $utilisateurId
        );

        if ($tache === null) {
            return null;
        }

        return array(
            'tache' => $tache,
            'rappels' => $this->rappelRepository->trouverParTache(
                (int) $tacheId
            )
        );
    }

    public function creer($tacheId, $utilisateurId, $dateRappel, $message)
    {
        $tache = $this->obtenirTacheAutorisee(
            $tacheId,
            $utilisateurId
        );

        if ($tache === null) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Cette tâche est introuvable ou inaccessible.'
            );
        }

        $rappel = new Rappel();
        $rappel->setTacheId((int) $tacheId);
        $rappel->setDateRappel($this->normaliserDate($dateRappel));
        $rappel->setMessage(trim($message));
        $rappel->setStatut('actif');

        if (!$this->validator->valider($rappel)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => ''
            );
        }

        try {
            $this->rappelRepository->ajouter($rappel);

            return array(
                'succes' => true,
                'erreurs' => array(),
                'message' => ''
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Impossible de créer le rappel.'
            );
        }
    }

    public function obtenirPourModification($id, $utilisateurId)
    {
        if (!$this->idValide($id)) {
            return null;
        }

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

        return array(
            'rappel' => $rappel,
            'tache' => $tache
        );
    }

    public function modifier(
        $id,
        $utilisateurId,
        $dateRappel,
        $message,
        $statut
    ) {
        $contexte = $this->obtenirPourModification(
            $id,
            $utilisateurId
        );

        if ($contexte === null) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Ce rappel est introuvable ou inaccessible.',
                'tacheId' => null
            );
        }

        $rappel = $contexte['rappel'];
        $tacheId = (int) $rappel->getTacheId();

        $rappel->setDateRappel($this->normaliserDate($dateRappel));
        $rappel->setMessage(trim($message));
        $rappel->setStatut(trim($statut));

        if (!$this->validator->valider($rappel)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => '',
                'tacheId' => $tacheId
            );
        }

        try {
            $this->rappelRepository->modifier($rappel);

            return array(
                'succes' => true,
                'erreurs' => array(),
                'message' => '',
                'tacheId' => $tacheId
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Impossible d’enregistrer les modifications.',
                'tacheId' => $tacheId
            );
        }
    }

    public function supprimer($id, $utilisateurId)
    {
        $contexte = $this->obtenirPourModification(
            $id,
            $utilisateurId
        );

        if ($contexte === null) {
            return array(
                'succes' => false,
                'tacheId' => null,
                'message' => 'Ce rappel est introuvable ou inaccessible.'
            );
        }

        $rappel = $contexte['rappel'];
        $tacheId = (int) $rappel->getTacheId();

        try {
            $succes = $this->rappelRepository->supprimer(
                $rappel->getId()
            );

            return array(
                'succes' => $succes,
                'tacheId' => $tacheId,
                'message' => $succes
                    ? ''
                    : 'Impossible de supprimer le rappel.'
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'tacheId' => $tacheId,
                'message' => 'Impossible de supprimer le rappel.'
            );
        }
    }

    private function obtenirTacheAutorisee($tacheId, $utilisateurId)
    {
        if (!$this->idValide($tacheId)
            || !$this->idValide($utilisateurId)) {
            return null;
        }

        $tache = $this->tacheRepository->trouverParId(
            (int) $tacheId
        );

        if (
            $tache === null
            || (int) $tache->getUtilisateurId() !== (int) $utilisateurId
        ) {
            return null;
        }

        return $tache;
    }

    private function normaliserDate($date)
    {
        return str_replace('T', ' ', trim((string) $date));
    }

    private function idValide($id)
    {
        return is_scalar($id)
            && is_numeric($id)
            && (int) $id > 0;
    }
}