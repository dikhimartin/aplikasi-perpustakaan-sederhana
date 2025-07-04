<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Utils.php';
require_once __DIR__ . '/StokBuku.php';

class Buku {
    private $conn;
    private $table_name = "MasterBuku";
    private $table_stok = "StokBuku";
    private $stokBukuModel;

    public $id_buku;
    public $judul;
    public $pengarang;
    public $penerbit;
    public $tahun_terbit;
    public $isbn;
    public $jumlah_tersedia; 

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
        $this->stokBukuModel = new StokBuku(); 
    }

    public function create() {
        $this->id_buku = Utils::generateUuid();

        $query = "INSERT INTO " . $this->table_name . "
                  SET id_buku=:id_buku, judul=:judul, pengarang=:pengarang,
                      penerbit=:penerbit, tahun_terbit=:tahun_terbit, isbn=:isbn";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->pengarang = htmlspecialchars(strip_tags($this->pengarang));
        $this->penerbit = htmlspecialchars(strip_tags($this->penerbit));
        $this->tahun_terbit = htmlspecialchars(strip_tags($this->tahun_terbit));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));

        // bind values
        $stmt->bindParam(":id_buku", $this->id_buku);
        $stmt->bindParam(":judul", $this->judul);
        $stmt->bindParam(":pengarang", $this->pengarang);
        $stmt->bindParam(":penerbit", $this->penerbit);
        $stmt->bindParam(":tahun_terbit", $this->tahun_terbit);
        $stmt->bindParam(":isbn", $this->isbn);

        if ($stmt->execute()) {
            $this->stokBukuModel->createStokBuku($this->id_buku, 0);
            return true;
        }

        return false;
    }

    public function readAll($limit = 10, $offset = 0) {
        $query = "SELECT mb.id_buku, mb.judul, mb.pengarang, mb.penerbit, mb.tahun_terbit, mb.isbn, sb.jumlah_tersedia
                  FROM " . $this->table_name . " mb
                  LEFT JOIN " . $this->table_stok . " sb ON mb.id_buku = sb.id_buku
                  ORDER BY mb.judul ASC
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

    public function findById($id_buku) {
        $query = "SELECT mb.id_buku, mb.judul, mb.pengarang, mb.penerbit, mb.tahun_terbit, mb.isbn, sb.jumlah_tersedia
                  FROM " . $this->table_name . " mb
                  LEFT JOIN " . $this->table_stok . " sb ON mb.id_buku = sb.id_buku
                  WHERE mb.id_buku = :id_buku
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_buku', $id_buku);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET judul=:judul, pengarang=:pengarang, penerbit=:penerbit,
                      tahun_terbit=:tahun_terbit, isbn=:isbn, updated_at=CURRENT_TIMESTAMP
                  WHERE id_buku=:id_buku";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->pengarang = htmlspecialchars(strip_tags($this->pengarang));
        $this->penerbit = htmlspecialchars(strip_tags($this->penerbit));
        $this->tahun_terbit = htmlspecialchars(strip_tags($this->tahun_terbit));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->id_buku = htmlspecialchars(strip_tags($this->id_buku));

        // bind values
        $stmt->bindParam(":judul", $this->judul);
        $stmt->bindParam(":pengarang", $this->pengarang);
        $stmt->bindParam(":penerbit", $this->penerbit);
        $stmt->bindParam(":tahun_terbit", $this->tahun_terbit);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":id_buku", $this->id_buku);

        if ($stmt->execute()) {
            return $this->stokBukuModel->updateStok($this->id_buku, $this->jumlah_tersedia);
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_buku = :id_buku";
        $stmt = $this->conn->prepare($query);

        $this->id_buku = htmlspecialchars(strip_tags($this->id_buku));
        $stmt->bindParam(':id_buku', $this->id_buku);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
