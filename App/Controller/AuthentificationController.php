<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Session.php';
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../Service/UtilisateurService.php';

class AuthentificationController extends Controller
{
    private $database;
    private $utilisateurService;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->utilisateurService = null;
    }

    private function obtenirUtilisateurService()
    {
        if ($this->utilisateurService === null) {
            $this->utilisateurService = new UtilisateurService(
                $this->database
            );
        }

        return $this->utilisateurService;
    }

    public function connexion()
    {
        Session::demarrer();

        if (Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $erreur = '';
        $email = '';

        if ($this->requeteEstPost()) {
            $email = trim(
                (string) $this->obtenirPost('email', '')
            );

            $motDePasse = (string) $this->obtenirPost(
                'mot_de_passe',
                ''
            );

            try {
                $resultat = $this->obtenirUtilisateurService()
                    ->authentifier($email, $motDePasse);

                if ($resultat['succes']) {
                    $utilisateur = $resultat['utilisateur'];

                    session_regenerate_id(true);

                    $_SESSION['utilisateur_id'] = $utilisateur->getId();
                    $_SESSION['utilisateur_nom'] = $utilisateur->getNom();
                    $_SESSION['utilisateur_prenom'] = $utilisateur->getPrenom();
                    $_SESSION['utilisateur_email'] = $utilisateur->getEmail();

                    $this->rediriger('/TaskFlow/Web/taches.php');
                }

                $erreur = $resultat['erreur'];

            } catch (PDOException $exception) {
                $erreur =
                    'Impossible de vous connecter pour le moment.';
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/connexion.php',
            array(
                'titrePage' => 'Connexion',
                'erreur' => $erreur,
                'email' => $email
            )
        );
    }

    public function inscription()
    {
        Session::demarrer();

        if (Session::estConnecte()) {
            $this->rediriger('/TaskFlow/Web/taches.php');
        }

        $erreur = '';
        $nom = '';
        $prenom = '';
        $email = '';

        if ($this->requeteEstPost()) {
            $nom = trim(
                (string) $this->obtenirPost('nom', '')
            );

            $prenom = trim(
                (string) $this->obtenirPost('prenom', '')
            );

            $email = trim(
                (string) $this->obtenirPost('email', '')
            );

            $motDePasse = (string) $this->obtenirPost(
                'mot_de_passe',
                ''
            );

            $confirmation = (string) $this->obtenirPost(
                'confirmation',
                ''
            );

            try {
                $resultat = $this->obtenirUtilisateurService()
                    ->inscrire(
                        $nom,
                        $prenom,
                        $email,
                        $motDePasse,
                        $confirmation
                    );

                if ($resultat['succes']) {
                    $this->rediriger('/TaskFlow/Web/connexion.php');
                }

                $erreur = $resultat['erreur'];

            } catch (PDOException $exception) {
                $erreur =
                    'Impossible de créer le compte pour le moment.';
            }
        }

        $this->afficherVue(
            __DIR__ . '/../../Templates/inscription.php',
            array(
                'titrePage' => 'Créer un compte',
                'erreur' => $erreur,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email
            )
        );
    }
}