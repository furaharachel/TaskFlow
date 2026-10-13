<?php

require_once __DIR__ . '/../../Config/Database.php';

/**
 * Classe abstraite commune aux repositories de TaskFlow.
 */
abstract class Repository
{
    protected $database;
    protected $pdo;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->pdo = $this->database->connect();
    }

    protected function getPdo()
    {
        return $this->pdo;
    }

    protected function idValide($id)
    {
        return is_numeric($id) && (int) $id > 0;
    }
}