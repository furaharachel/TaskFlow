<?php

require_once __DIR__ . '/../App/Session.php';

Session::deconnecter();

header('Location: /TaskFlow/Web/connexion.php');
exit;
