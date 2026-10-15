<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Service/CategorieService.php';
require_once __DIR__ . '/../../Config/Database.php';

class CategorieController extends Controller
{
    private $categorieService;

    public function __construct(Database $database)
    {
        $this->categorieService = new CategorieService($database);
    }

    public function index()
    {
        $this->afficherListe();
    }

    public function creer()
    {
        $erreurs = array();
        $message = '';
        $nom = '';
        $description = '';

        if ($this->requeteEstPost()) {
            $nom = $this->obtenirPost('nom');
            $description = $this->obtenirPost('description');

            $resultat = $this->categorieService->creer(
                trim($nom),
                trim($description)
            );

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];

            if ($resultat['succes']) {
                $this->rediriger('categories.php');
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

    public function modifier($id)
    {
        if (!$this->idValide($id)) {
            $this->rediriger('categories.php');
        }

        $id = (int) $id;

        $categorie = $this->categorieService->trouverParId($id);

        if ($categorie === null) {
            $this->rediriger('categories.php');
        }

        $erreurs = array();
        $message = '';

        if ($this->requeteEstPost()) {
            $nom = $this->obtenirPost('nom');
            $description = $this->obtenirPost('description');

            $resultat = $this->categorieService->modifier(
                $categorie,
                trim($nom),
                trim($description)
            );

            $erreurs = $resultat['erreurs'];
            $message = $resultat['message'];

            if ($resultat['succes']) {
                $this->rediriger('categories.php');
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

    public function supprimer($id)
    {
        $resultat = $this->categorieService->supprimer($id);

        if (!$resultat['succes']) {
            $this->afficherListe($resultat['message']);
            return;
        }

        $this->rediriger('categories.php');
    }

    private function afficherListe($erreur = '')
    {
        $categories = array();

        try {
            $categories = $this->categorieService->lister();
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

    private function idValide($id)
    {
        return is_scalar($id)
            && is_numeric($id)
            && (int) $id > 0;
    }
}