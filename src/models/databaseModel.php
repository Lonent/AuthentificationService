<?php

class Database {
    protected $host;
    protected $dbname;
    protected $username;
    protected $password;

    private $pdo

    function __construct($host, $dbname, $username, $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    function connect(){
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host}; dbname={$this->dbname};",
                $this->username,
                $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    function create($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch {
            return false;
        }
    }
    function read($table, $conditions=[], $columns)
    {
        $sql = "SELECT {$columns} FROM {$table}";

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach($conditions as $column => $value) {
                $whereClauses = "{$column} =:{$value}";
            }
            $sql .=" WHERE " . implode(' AND ', $whereClauses);
        }
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($conditions);
            return $stmt->fetchAll();
        } catch(PDOException $exception) {
            return false;
        }
    }
    function update($table, $data, $conditions)
    {
        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = :{$column}";
        }

        $whereClauses = [];
        foreach($conditions as $column => $value) {
            $whereClauses = "{$column} =:{$value}";
        }

        $sql = "UPDATE {$table} SET" .
            implode(', ', $setClauses) .
            " WHERE " . implode(' AND ', $whereClauses);
        $params = array_merge($data, array_combine(array_map(function ($key) {
            return "condition_{$key}";
        }, array_keys($conditions)), $conditions));

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $exception) {
            return false;
        }
    }

    function delete($table, $conditions)
    {
        $whereClauses = [];
        foreach($conditions as $column => $value) {
            $whereClauses = "{$column} =:{$value}";
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClauses);
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($conditions);
        } catch (PDOException $exception) {
            return false;
        }
    }
}