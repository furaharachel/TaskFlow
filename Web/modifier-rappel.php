<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Controller/RappelController.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$controller = new RappelController(new Database());
$controller->modifier($id);