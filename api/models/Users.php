<?php
require_once "auth.php";

class Users
{
    private $api_token = "APITOKEN";

    private $conn;
    private $table_name = "users";


    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function read()
    {
        $query = "SELECT * FROM ". $this->table_name . ";";
            
        $result = $this->conn->prepare($query);
        $result->execute();
        return $result;
    }

    public function create($email, $phone_number, $first_name, $last_name, $password)
    {
        $authCode = getBearerToken();
        if($authCode != $this->api_token) return;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO " . $this->table_name . " (`email`, `phone_number`, `first_name`, `last_name`, `password`) VALUES (?, ?, ?, ?, ?)";

        $result = $this->conn->prepare($query);
        return $result->execute([$email, $phone_number, $first_name, $last_name, $hashedPassword]);
    }

    public function update($email, $attribute, $value)
    {
        $query = "UPDATE " . $this->table_name . " SET " . $attribute . " = :value WHERE email = :email";

        $result = $this->conn->prepare($query);
        $result->bindParam(':value', $value);
        $result->bindParam(':email', $email);

        return $result->execute();
    }

    public function delete($email)
    {
        $query = "DELETE FROM ". $this->table_name ." WHERE email = :email";
        $result = $this->conn->prepare($query);
        $result->bindParam(':email', $email);

        return $result->execute();
    }

    public function doesExist($email, $phone_number)
    {
        $authCode = getBearerToken();
        $query = "SELECT id FROM ". $this->table_name . " WHERE email = :email OR phone_number = :phone_number";
        
        $result = $this->conn->prepare($query);
        $result->bindParam(":email", $email);
        $result->bindParam("phone_number", $phone_number);
        $result->execute();

        $rows = $result->rowCount();

        return $rows > 0 ? true : false;
    }

    public function login($email, $password)
    {
        $query = "SELECT id, email, phone_number, first_name, last_name, password FROM ". $this->table_name ." WHERE email = :email";
    
        $result = $this->conn->prepare($query);
        $result->bindParam(":email", $email);
        $result->execute();

        $user = $result->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) 
        {
            unset($user['password']);
            return $user;
        }

        return null;
    }
}
?>