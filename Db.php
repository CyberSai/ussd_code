<?php

class Db {
    protected static $connection;

    private function __construct() { }

    public static function getInstance()
    {
        if (self::$connection == null) {
            self::$connection = mysqli_connect(
                'localhost',
                'root',
                'cyber_mysql',
                'ussd_code',
                3306
            );

            if (mysqli_connect_errno()) {
                die('Failed to connect to database ' . mysqli_connect_error());
            }
        }

        return new self();
    }

    public function init()
    {
        $sql = <<<SQL
CREATE TABLE records (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    network VARCHAR(20),
    stage VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
SQL;
        if(self::$connection->query($sql)) {
            echo 'Created database successfully';
        } else {
            echo 'Could not created database' . self::$connection->error;
        }
    }

    public function insert($data)
    {
        $keys = implode(', ', array_keys($data));
        $values = implode(', ', array_map(function($value) {
            return "'{$value}'";
        }, array_values($data)));
        $sql = "INSERT INTO records ($keys) VALUES ($values)";

        return self::$connection->query($sql);
    }

    public function get($sessionid)
    {
        $sql = "SELECT * FROM records where session_id = '{$sessionid}' LIMIT 1";
        $results = self::$connection->query($sql);

        if ($results->num_rows == 1) {
            return $results->fetch_assoc();
        }

        return false;
    }

    public function update($id, $data)
    {
        $information = implode(', ', array_map(function ($value) use ($data) {
            return "{$value} = '{$data[$value]}'";
        }, array_keys($data)));
        $sql = "UPDATE records SET {$information} WHERE id='{$id}'";

        return self::$connection->query($sql);
    }

    public function exit()
    {
        self::$connection->close();
    }
}