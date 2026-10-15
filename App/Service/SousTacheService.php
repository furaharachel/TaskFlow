<?php

require_once __DIR__ . '/../Repository/TacheRepository.php';
require_once __DIR__ . '/../Repository/SousTacheRepository.php';
require_once __DIR__ . '/../Validation/SousTacheValidator.php';
require_once __DIR__ . '/../Entity/SousTache.php';
require_once __DIR__ . '/../../Config/Database.php';

class SousTacheService
{
    private $tacheRepository;
    private $sousTacheRepository;
    private $validator;

    public function __construct(Database $database)
    {
        $this->tacheRepository = new TacheRepository($database);
        $this->sousTacheRepository = new SousTacheRepository($database);
        $this->validator = new SousTacheValidator();
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

        $sousTaches = $this->sousTacheRepository->trouverParTache(
            $tacheId
        );

        $total = count($sousTaches);
        $terminees = 0;

        foreach ($sousTaches as $sousTache) {
            if ($sousTache->getStatut() === 'terminee') {
                $terminees++;
            }
        }

        $progression = 0;

        if ($total > 0) {
            $progression = (int) round(
                ($terminees / $total) * 100
            );
        }

        return array(
            'tache' => $tache,
            'sousTaches' => $sousTaches,
            'nombreSousTaches' => $total,
            'nombreSousTachesTerminees' => $terminees,
            'progression' => $progression
        );
    }

    public function creer($tacheId, $utilisateurId, $titre, $statut)
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

        $sousTache = new SousTache();
        $sousTache->setTacheId((int) $tacheId);
        $sousTache->setTitre($titre);
        $sousTache->setStatut($statut);

        if (!$this->validator->valider($sousTache)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => 'Veuillez corriger les erreurs du formulaire.'
            );
        }

        try {
            $this->sousTacheRepository->ajouter($sousTache);

            return array(
                'succes' => true,
                'erreurs' => array(),
                'message' => ''
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Impossible de créer la sous-tâche.'
            );
        }
    }

    public function obtenirPourModification($id, $utilisateurId)
    {
        return $this->obtenirSousTacheAutorisee(
            $id,
            $utilisateurId
        );
    }

    public function modifier($id, $utilisateurId, $titre, $statut)
    {
        $sousTache = $this->obtenirSousTacheAutorisee(
            $id,
            $utilisateurId
        );

        if ($sousTache === null) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Cette sous-tâche est introuvable ou inaccessible.',
                'tacheId' => null
            );
        }

        $tacheId = (int) $sousTache->getTacheId();

        $sousTache->setTitre($titre);
        $sousTache->setStatut($statut);

        if (!$this->validator->valider($sousTache)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => 'Veuillez corriger les erreurs du formulaire.',
                'tacheId' => $tacheId
            );
        }

        try {
            $this->sousTacheRepository->modifier($sousTache);

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
                'message' => 'Impossible de modifier la sous-tâche.',
                'tacheId' => $tacheId
            );
        }
    }

    public function supprimer($id, $utilisateurId)
    {
        $sousTache = $this->obtenirSousTacheAutorisee(
            $id,
            $utilisateurId
        );

        if ($sousTache === null) {
            return array(
                'succes' => false,
                'tacheId' => null,
                'message' => 'Cette sous-tâche est introuvable ou inaccessible.'
            );
        }

        $tacheId = (int) $sousTache->getTacheId();

        try {
            $succes = $this->sousTacheRepository->supprimer(
                $sousTache->getId()
            );

            return array(
                'succes' => $succes,
                'tacheId' => $tacheId,
                'message' => $succes
                    ? ''
                    : 'Impossible de supprimer la sous-tâche.'
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'tacheId' => $tacheId,
                'message' => 'Impossible de supprimer la sous-tâche.'
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

    private function obtenirSousTacheAutorisee($id, $utilisateurId)
    {
        if (!$this->idValide($id)
            || !$this->idValide($utilisateurId)) {
            return null;
        }

        $sousTache = $this->sousTacheRepository->trouverParId(
            (int) $id
        );

        if ($sousTache === null) {
            return null;
        }

        $tache = $this->obtenirTacheAutorisee(
            $sousTache->getTacheId(),
            $utilisateurId
        );

        if ($tache === null) {
            return null;
        }

        return $sousTache;
    }

    private function idValide($id)
    {
        return is_scalar($id)
            && is_numeric($id)
            && (int) $id > 0;
    }
}