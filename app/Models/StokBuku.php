<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Utils.php';

class StokBuku {
    private $conn;
    private $table_name = "StokBuku"; 

    public $id_stok;
    public $id_buku;
    public $jumlah_tersedia;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function createStokBuku($id_buku, $jumlah) {
        $this->id_stok = Utils::generateUuid();

        $query = "INSERT INTO " . $this->table_name . "
                  SET id_stok=:id_stok, id_buku=:id_buku, jumlah_tersedia=:jumlah_tersedia";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $id_buku = htmlspecialchars(strip_tags($id_buku));
        $jumlah = htmlspecialchars(strip_tags($jumlah));

        // Bind parameter
        $stmt->bindParam(":id_stok", $this->id_stok);
        $stmt->bindParam(":id_buku", $id_buku);
        $stmt->bindParam(":jumlah_tersedia", $jumlah, PDO::PARAM_INT); 

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getJumlahTersedia($id_buku) {
        $query = "SELECT jumlah_tersedia FROM " . $this->table_name . " WHERE id_buku = :id_buku LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_buku", $id_buku);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['jumlah_tersedia'] : 0;
    }

   
    public function updateStok($id_buku, $jumlah_baru) {
        $query = "UPDATE " . $this->table_name . "
                  SET jumlah_tersedia=:jumlah_tersedia, updated_at=CURRENT_TIMESTAMP
                  WHERE id_buku=:id_buku";
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $id_buku = htmlspecialchars(strip_tags($id_buku));
        $jumlah_baru = htmlspecialchars(strip_tags($jumlah_baru));

        // Bind parameter
        $stmt->bindParam(":jumlah_tersedia", $jumlah_baru, PDO::PARAM_INT);
        $stmt->bindParam(":id_buku", $id_buku);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
