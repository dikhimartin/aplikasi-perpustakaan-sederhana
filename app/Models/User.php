<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Utils.php';

class User {
    private $conn;
    private $table_name = "User";

    public $id_user;
    public $username;
    public $password; 
    public $role;
    public $nim_mahasiswa;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function findByUsername($username) {
        $query = "SELECT id_user, username, password, role, nim_mahasiswa FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function create() {
        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT); 

        $query = "INSERT INTO " . $this->table_name . "
                  SET id_user=:id_user, username=:username, password=:password,
                      role=:role, nim_mahasiswa=:nim_mahasiswa";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $nim_mahasiswa_sanitized = $this->nim_mahasiswa ? htmlspecialchars(strip_tags($this->nim_mahasiswa)) : null;

        // bind values
        $stmt->bindParam(":id_user", $this->id_user);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $hashed_password); // Bind hashed password
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":nim_mahasiswa", $nim_mahasiswa_sanitized);

        if ($stmt->execute()) {
            return true;
        }

        error_log("Error creating user: " . implode(" - ", $stmt->errorInfo()));
        return false;
    }
}
