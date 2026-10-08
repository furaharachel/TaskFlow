<?php

class Database
{
    private $host = 'localhost';
    private $database = 'taskflow';
    private $username = 'root';
    private $password = '';

    private $connection = null;

    public function connect()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $this->connection = new PDO(
            'mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=utf8',
            $this->username,
            $this->password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            )
        );

        return $this->connection;
    }
}