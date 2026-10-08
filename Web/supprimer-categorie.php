<?php

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Repository/CategorieRepository.php';

$database = new Database();

$categorieRepository = new CategorieRepository($database);

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header('Location: categories.php');
    exit;
}

try {

    $categorieRepository->supprimer($id);

} catch (PDOException $exception) {

    header('Location: categories.php');
    exit;
}

header('Location: categories.php');
exit;