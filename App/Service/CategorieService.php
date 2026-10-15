<?php

require_once __DIR__ . '/../Repository/CategorieRepository.php';
require_once __DIR__ . '/../Validation/CategorieValidator.php';
require_once __DIR__ . '/../Entity/Categorie.php';
require_once __DIR__ . '/../../Config/Database.php';

class CategorieService
{
    private $categorieRepository;
    private $validator;

    public function __construct(Database $database)
    {
        $this->categorieRepository = new CategorieRepository($database);
        $this->validator = new CategorieValidator();
    }

    public function lister()
    {
        return $this->categorieRepository->trouverTous();
    }

    public function trouverParId($id)
    {
        if (!$this->idValide($id)) {
            return null;
        }

        return $this->categorieRepository->trouverParId((int) $id);
    }

    public function creer($nom, $description)
    {
        $categorie = new Categorie();
        $categorie->setNom($nom);
        $categorie->setDescription($description);

        if (!$this->validator->valider($categorie)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => ''
            );
        }

        try {
            $this->categorieRepository->ajouter($categorie);

            return array(
                'succes' => true,
                'erreurs' => array(),
                'message' => ''
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Impossible de créer la catégorie.'
            );
        }
    }

    public function modifier(Categorie $categorie, $nom, $description)
    {
        $categorie->setNom($nom);
        $categorie->setDescription($description);

        if (!$this->validator->valider($categorie)) {
            return array(
                'succes' => false,
                'erreurs' => $this->validator->getErreurs(),
                'message' => ''
            );
        }

        try {
            $this->categorieRepository->modifier($categorie);

            return array(
                'succes' => true,
                'erreurs' => array(),
                'message' => ''
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'erreurs' => array(),
                'message' => 'Impossible de modifier la catégorie.'
            );
        }
    }

    public function supprimer($id)
    {
        if (!$this->idValide($id)) {
            return array(
                'succes' => false,
                'message' => 'Identifiant de catégorie invalide.'
            );
        }

        try {
            $this->categorieRepository->supprimer((int) $id);

            return array(
                'succes' => true,
                'message' => ''
            );
        } catch (PDOException $exception) {
            return array(
                'succes' => false,
                'message' => 'Impossible de supprimer la catégorie.'
            );
        }
    }

    private function idValide($id)
    {
        return is_numeric($id)
            && (int) $id > 0
            && (string) (int) $id === (string) $id;
    }
}