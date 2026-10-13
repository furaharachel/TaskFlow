<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Entity/Categorie.php';
require_once __DIR__ . '/../Repository/CategorieRepository.php';
require_once __DIR__ . '/../Validation/CategorieValidator.php';

/**
 * Contrôleur responsable de la gestion des catégories.
 */
class CategorieController extends Controller
{
    private $categorieRepository;
    private $validator;

    public function __construct(Database $database)
    {
        $this->categorieRepository = new CategorieRepository($database);
        $this->validator = new CategorieValidator();
    }

    /**
     * Affiche la liste des catégories.
     */
    public function index()
    {
        $this->afficherListe();
    }

    /**
     * Affiche et traite le formulaire de création.
     */
    public function creer()
    {
        $erreurs = array();
        $message = '';
        $nom = '';
        $description = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = isset($_POST['nom'])
                ? trim($_POST['nom'])
                : '';

            $description = isset($_POST['description'])
                ? trim($_POST['description'])
                : '';

            $categorie = new Categorie();
            $categorie->setNom($nom);
            $categorie->setDescription($description);

            if ($this->validator->valider($categorie)) {
                try {
                    $this->categorieRepository->ajouter($categorie);
                    $this->rediriger('categories.php');
                } catch (PDOException $exception) {
                    $message = 'Impossible de créer la catégorie.';
                }
            } else {
                $erreurs = $this->validator->getErreurs();
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/creer-categorie.php',
            array(
                'titrePage' => 'Créer une catégorie',
                'erreurs' => $erreurs,
                'message' => $message,
                'nom' => $nom,
                'description' => $description
            )
        );
    }

    /**
     * Affiche et traite le formulaire de modification.
     */
    public function modifier($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            $this->rediriger('categories.php');
        }

        $categorie = $this->categorieRepository->trouverParId($id);

        if ($categorie === null) {
            $this->rediriger('categories.php');
        }

        $erreurs = array();
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = isset($_POST['nom'])
                ? trim($_POST['nom'])
                : '';

            $description = isset($_POST['description'])
                ? trim($_POST['description'])
                : '';

            $categorie->setNom($nom);
            $categorie->setDescription($description);

            if ($this->validator->valider($categorie)) {
                try {
                    $this->categorieRepository->modifier($categorie);
                    $this->rediriger('categories.php');
                } catch (PDOException $exception) {
                    $message = 'Impossible de modifier la catégorie.';
                }
            } else {
                $erreurs = $this->validator->getErreurs();
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/modifier-categorie.php',
            array(
                'titrePage' => 'Modifier une catégorie',
                'categorie' => $categorie,
                'id' => $id,
                'erreurs' => $erreurs,
                'message' => $message
            )
        );
    }

    /**
     * Supprime une catégorie.
     */
    public function supprimer($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            $this->rediriger('categories.php');
        }

        try {
            $this->categorieRepository->supprimer($id);
        } catch (PDOException $exception) {
            $this->afficherListe(
                'Impossible de supprimer la catégorie.'
            );
            return;
        }

        $this->rediriger('categories.php');
    }

    /**
     * Charge les catégories et affiche leur vue.
     */
    private function afficherListe($erreur = '')
    {
        $categories = array();

        try {
            $categories = $this->categorieRepository->trouverTous();
        } catch (PDOException $exception) {
            $erreur = 'Impossible de charger les catégories.';
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/categories.php',
            array(
                'titrePage' => 'Catégories',
                'categories' => $categories,
                'erreur' => $erreur
            )
        );
    }
}