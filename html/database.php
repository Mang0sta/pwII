<?php

class Database {
    private $host = 'localhost:3306'; 
    private $dbname = 'db_users'; 
    private $username = 'root'; 
    private $password = ''; 
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                die("Error de conexión a la base de datos: " . $this->conn->connect_error);
            }

            //charset a utf8
            $this->conn->set_charset("utf8");

        } catch (Exception $e) {
            die("Error al inicializar la conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}

?>