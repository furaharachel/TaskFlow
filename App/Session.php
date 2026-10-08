<?php

class Session
{
    public static function demarrer()
    {
        if (session_id() === '') {
            session_start();
        }
    }

    public static function estConnecte()
    {
        self::demarrer();

        return isset($_SESSION['utilisateur_id']);
    }

    public static function obtenirUtilisateurId()
    {
        self::demarrer();

        if (!isset($_SESSION['utilisateur_id'])) {
            return null;
        }

        return (int) $_SESSION['utilisateur_id'];
    }

    public static function obtenirPrenom()
    {
        self::demarrer();

        if (!isset($_SESSION['utilisateur_prenom'])) {
            return '';
        }

        return $_SESSION['utilisateur_prenom'];
    }

    public static function obtenirNom()
    {
        self::demarrer();

        if (!isset($_SESSION['utilisateur_nom'])) {
            return '';
        }

        return $_SESSION['utilisateur_nom'];
    }

    public static function obtenirEmail()
    {
        self::demarrer();

        if (!isset($_SESSION['utilisateur_email'])) {
            return '';
        }

        return $_SESSION['utilisateur_email'];
    }

    public static function connecter($utilisateur)
    {
        self::demarrer();

        session_regenerate_id(true);

        $_SESSION['utilisateur_id'] = $utilisateur->getId();
        $_SESSION['utilisateur_nom'] = $utilisateur->getNom();
        $_SESSION['utilisateur_prenom'] = $utilisateur->getPrenom();
        $_SESSION['utilisateur_email'] = $utilisateur->getEmail();
    }

    public static function deconnecter()
    {
        self::demarrer();

        $_SESSION = array();

        if (ini_get('session.use_cookies')) {

            $parametres = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametres['path'],
                $parametres['domain'],
                $parametres['secure'],
                $parametres['httponly']
            );
        }

        session_destroy();
    }
}