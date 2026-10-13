<?php

/**
 * Classe abstraite de base de tous les contrôleurs TaskFlow.
 *
 * Responsabilités :
 * - afficher les vues ;
 * - gérer les redirections ;
 * - sécuriser les valeurs affichées ;
 * - centraliser les vérifications des requêtes HTTP ;
 * - récupérer proprement les données des formulaires et URL.
 *
 * La logique métier reste dans les contrôleurs spécialisés.
 */
abstract class Controller
{
    /**
     * Charge une vue et lui transmet ses données.
     *
     * @param string $chemin Chemin complet du fichier de vue.
     * @param array $donnees Données accessibles dans la vue.
     */
    protected function afficherVue($chemin, $donnees = array())
    {
        if (!is_array($donnees)) {
            throw new InvalidArgumentException(
                'Les données de la vue doivent être un tableau.'
            );
        }

        if (!is_file($chemin)) {
            throw new RuntimeException(
                'Vue introuvable : ' . $chemin
            );
        }

        extract($donnees, EXTR_SKIP);

        require $chemin;
    }

    /**
     * Redirige vers une autre page et arrête le traitement.
     *
     * @param string $url Adresse de destination.
     */
    protected function rediriger($url)
    {
        if (!is_string($url) || trim($url) === '') {
            throw new InvalidArgumentException(
                'L’adresse de redirection est invalide.'
            );
        }

        if (headers_sent($fichier, $ligne)) {
            throw new RuntimeException(
                'Redirection impossible : les en-têtes ont déjà été '
                . 'envoyés dans ' . $fichier
                . ' à la ligne ' . $ligne . '.'
            );
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Protège une valeur avant son affichage dans du HTML.
     *
     * @param mixed $valeur Valeur à échapper.
     * @return string
     */
    protected function echapper($valeur)
    {
        return htmlspecialchars(
            (string) $valeur,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    /**
     * Vérifie si la requête HTTP utilise la méthode POST.
     *
     * @return bool
     */
    protected function requeteEstPost()
    {
        return isset($_SERVER['REQUEST_METHOD'])
            && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    /**
     * Récupère une valeur simple envoyée par un formulaire.
     *
     * Ne remplace pas la validation métier : celle-ci reste
     * à la charge du validateur approprié.
     *
     * @param string $cle Nom du champ.
     * @param mixed $valeurParDefaut Valeur utilisée si le champ manque.
     * @return mixed
     */
    protected function obtenirPost($cle, $valeurParDefaut = '')
    {
        if (
            !isset($_POST[$cle])
            || !is_scalar($_POST[$cle])
        ) {
            return $valeurParDefaut;
        }

        return $_POST[$cle];
    }

    /**
     * Récupère une valeur simple provenant de l'URL.
     *
     * @param string $cle Nom du paramètre.
     * @param mixed $valeurParDefaut Valeur utilisée si le paramètre manque.
     * @return mixed
     */
    protected function obtenirGet($cle, $valeurParDefaut = '')
    {
        if (
            !isset($_GET[$cle])
            || !is_scalar($_GET[$cle])
        ) {
            return $valeurParDefaut;
        }

        return $_GET[$cle];
    }
}