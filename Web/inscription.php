<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Controller/AuthentificationController.php';

$controller = new AuthentificationController(
    new Database()
);

$controller->inscription();