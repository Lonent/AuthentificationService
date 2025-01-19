<?php
namespace App\Models;

class DatabaseModel {
    function __construct()
    {
        $config = require __DIR__ . '/../Config/dbconfig.php';
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        $this->connect();
    }

    function connect(){
        try {
            $this->pdo = new \PDO(
                "mysql:host={$this->host}; dbname={$this->dbname};",
                $this->username,
                $this->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return true;
        } catch (\PDOException $exception) {
            return false;
        }
    }

    /**
     * Вставляет новую запись в таблицу.
     *
     * @param string $table Название таблицы.
     * @param array $data Ассоциативный массив данных для вставки (ключи — это названия колонок) Например 'login' => 'TestLogin'.
     * @return int|bool Возвращает ID последней вставленной записи или false в случае ошибки.
     */
    function create($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch(\PDOException $exception) {
            return false;
        }
    }

    /**
     * Получает записи из таблицы.
     *
     * @param string $table Название таблицы.
     * @param array $columns Массив с названиями колонок.
     * @param array $conditions Ассоциативный массив условий (по умолчанию пустой).
     * @return array|bool Возвращает массив записей или false в случае ошибки.
     */
    function read($table, $columns, $conditions=[])
    {
        $columns = implode(', ', $columns);
        $sql = "SELECT {$columns} FROM {$table}";

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
            }
            $sql .=" WHERE " . implode(' AND ', $whereClauses);
        }
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($conditions);
            return $stmt->fetchAll();
        } catch(\PDOException $exception) {
            return false;
        }
    }

    /**
     * Обновляет записи в таблице.
     *
     * @param string $table Название таблицы.
     * @param array $data Ассоциативный массив обновляемых данных (ключи — это названия колонок).
     * @param array $conditions Ассоциативный массив условий для обновления.
     * @return bool Возвращает true при успешном обновлении, иначе false.
     */
    function update($table, $data, $conditions)
    {
        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = :{$column}";
        }

        $whereClauses = [];
        foreach($conditions as $column => $value) {
            $whereClauses[] = "{$column} = :{$column}";
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
        } catch(\PDOException $exception) {
            return false;
        }
    }

    /**
     * Удаляет записи из таблицы.
     *
     * @param string $table Название таблицы.
     * @param array $conditions Ассоциативный массив условий для удаления.
     * @return bool Возвращает true при успешном удалении, иначе false.
     */
    function delete($table, $conditions)
    {
        $whereClauses = [];
        foreach($conditions as $column => $value) {
            $whereClauses[] = "{$column} = :{$column}";
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClauses);
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($conditions);
        } catch (\PDOException $exception) {
            return false;
        }
    }
}