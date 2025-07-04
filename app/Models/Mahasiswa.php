<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Utils.php';

class Mahasiswa {
    private $conn;
    private $table_name = "MasterMahasiswa";

    public $nim;
    public $nama_mahasiswa;
    public $jurusan;
    public $email;
    public $no_telepon;
    public $status;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $this->nim = Utils::generateUuid(); 

        $query = "INSERT INTO " . $this->table_name . "
                  SET nim=:nim, nama_mahasiswa=:nama_mahasiswa, jurusan=:jurusan,
                      email=:email, no_telepon=:no_telepon, status=:status";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nama_mahasiswa = htmlspecialchars(strip_tags($this->nama_mahasiswa));
        $this->jurusan = htmlspecialchars(strip_tags($this->jurusan));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->no_telepon = htmlspecialchars(strip_tags($this->no_telepon));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind values
        $stmt->bindParam(":nim", $this->nim);
        $stmt->bindParam(":nama_mahasiswa", $this->nama_mahasiswa);
        $stmt->bindParam(":jurusan", $this->jurusan);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":no_telepon", $this->no_telepon);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readAll($limit = 10, $offset = 0) {
        $query = "SELECT nim, nama_mahasiswa, jurusan, email, no_telepon, status
                  FROM " . $this->table_name . "
                  ORDER BY nama_mahasiswa ASC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

    public function findById($nim) {
        $query = "SELECT nim, nama_mahasiswa, jurusan, email, no_telepon, status
                  FROM " . $this->table_name . "
                  WHERE nim = :nim
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET nama_mahasiswa=:nama_mahasiswa, jurusan=:jurusan,
                      email=:email, no_telepon=:no_telepon, status=:status,
                      updated_at=CURRENT_TIMESTAMP
                  WHERE nim=:nim";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nama_mahasiswa = htmlspecialchars(strip_tags($this->nama_mahasiswa));
        $this->jurusan = htmlspecialchars(strip_tags($this->jurusan));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->no_telepon = htmlspecialchars(strip_tags($this->no_telepon));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->nim = htmlspecialchars(strip_tags($this->nim));

        // bind values
        $stmt->bindParam(":nama_mahasiswa", $this->nama_mahasiswa);
        $stmt->bindParam(":jurusan", $this->jurusan);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":no_telepon", $this->no_telepon);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":nim", $this->nim);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE nim = :nim";
        $stmt = $this->conn->prepare($query);

        $this->nim = htmlspecialchars(strip_tags($this->nim));
        $stmt->bindParam(':nim', $this->nim);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}