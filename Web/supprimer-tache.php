<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Controller/TacheController.php';

$database = new Database();
$controller = new TacheController($database);

$id = isset($_GET['id']) && is_scalar($_GET['id'])
    ? $_GET['id']
    : 0;

$controller->supprimer($id);