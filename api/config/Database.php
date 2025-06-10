<?php
class Database 
{
    private $conn;

    private $host = "host";
    private $db_name = "dbName";
    private $username = "username";
    private $password = "password";

    public function getConnection()
    {
        $this->conn = null;

        try
        {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }
        catch(PDOException $e)
        {
            echo "Failed to connect: ". $e->getMessage();
        }

        return $this->conn;
    }
}

?>