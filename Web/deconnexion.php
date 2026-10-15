<?php

require_once __DIR__ . '/../App/Session.php';

Session::demarrer();

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

header('Location: /TaskFlow/Web/connexion.php');
exit;