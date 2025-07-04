<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Utils.php';
require_once __DIR__ . '/Mahasiswa.php';
require_once __DIR__ . '/Buku.php';
require_once __DIR__ . '/StokBuku.php'; 

class Peminjaman {
    private $conn;
    private $table_peminjaman = "TransaksiPeminjaman";
    private $table_stok = "StokBuku";
    private $table_history = "HistoryPeminjaman";

    private $bukuModel;
    private $mahasiswaModel;
    private $stokBukuModel;

    public $id_peminjaman;
    public $nim_mahasiswa;
    public $id_buku;
    public $tanggal_pinjam;
    public $tanggal_kembali_maksimal;
    public $tanggal_kembali_aktual;
    public $status;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
        $this->bukuModel = new Buku();
        $this->mahasiswaModel = new Mahasiswa();
        $this->stokBukuModel = new StokBuku(); 
    }

    public function create() {
        $this->id_peminjaman = Utils::generateUuid();

        $stok_tersedia = $this->stokBukuModel->getJumlahTersedia($this->id_buku);

        if ($stok_tersedia <= 0) {
            $_SESSION['error'] = "Stok buku tidak tersedia.";
            return false;
        }

        $mahasiswa_data = $this->mahasiswaModel->findById($this->nim_mahasiswa);
        if (!$mahasiswa_data || $mahasiswa_data['status'] !== 'aktif') {
            $_SESSION['error'] = "Mahasiswa tidak aktif atau tidak ditemukan.";
            return false;
        }

        $tanggal_pinjam_obj = new DateTime($this->tanggal_pinjam);
        $tanggal_kembali_maksimal_obj = clone $tanggal_pinjam_obj;
        $tanggal_kembali_maksimal_obj->modify('+2 weeks');
        $this->tanggal_kembali_maksimal = $tanggal_kembali_maksimal_obj->format('Y-m-d');

        $stmt_borrowed = $this->conn->prepare("SELECT COUNT(*) FROM " . $this->table_peminjaman . " WHERE nim_mahasiswa = :nim_mahasiswa AND id_buku = :id_buku AND status = 'dipinjam'");
        $stmt_borrowed->bindParam(":nim_mahasiswa", $this->nim_mahasiswa);
        $stmt_borrowed->bindParam(":id_buku", $this->id_buku);
        $stmt_borrowed->execute();
        if ($stmt_borrowed->fetchColumn() > 0) {
            $_SESSION['error'] = "Buku ini sedang dipinjam oleh mahasiswa tersebut.";
            return false;
        }


        $query = "INSERT INTO " . $this->table_peminjaman . "
                  SET id_peminjaman=:id_peminjaman, nim_mahasiswa=:nim_mahasiswa,
                      id_buku=:id_buku, tanggal_pinjam=:tanggal_pinjam,
                      tanggal_kembali_maksimal=:tanggal_kembali_maksimal, status='dipinjam'";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nim_mahasiswa = htmlspecialchars(strip_tags($this->nim_mahasiswa));
        $this->id_buku = htmlspecialchars(strip_tags($this->id_buku));
        $this->tanggal_pinjam = htmlspecialchars(strip_tags($this->tanggal_pinjam));

        // bind values
        $stmt->bindParam(":id_peminjaman", $this->id_peminjaman);
        $stmt->bindParam(":nim_mahasiswa", $this->nim_mahasiswa);
        $stmt->bindParam(":id_buku", $this->id_buku);
        $stmt->bindParam(":tanggal_pinjam", $this->tanggal_pinjam);
        $stmt->bindParam(":tanggal_kembali_maksimal", $this->tanggal_kembali_maksimal);

        if ($stmt->execute()) {
            $new_stok = $stok_tersedia - 1;
            $this->stokBukuModel->updateStok($this->id_buku, $new_stok);
            return true;
        }

        return false;
    }

    public function returnBook($id_peminjaman) {
        $query = "SELECT id_buku, nim_mahasiswa, tanggal_pinjam FROM " . $this->table_peminjaman . " WHERE id_peminjaman = :id_peminjaman AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_peminjaman", $id_peminjaman);
        $stmt->execute();
        $peminjaman_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$peminjaman_data) {
            $_SESSION['error'] = "Peminjaman tidak ditemukan atau sudah dikembalikan.";
            return false;
        }

        $tanggal_kembali_aktual = date('Y-m-d');
        $query_update = "UPDATE " . $this->table_peminjaman . "
                         SET tanggal_kembali_aktual = :tanggal_kembali_aktual, status = 'dikembalikan', updated_at = CURRENT_TIMESTAMP
                         WHERE id_peminjaman = :id_peminjaman";
        $stmt_update = $this->conn->prepare($query_update);
        $stmt_update->bindParam(":tanggal_kembali_aktual", $tanggal_kembali_aktual);
        $stmt_update->bindParam(":id_peminjaman", $id_peminjaman);

        if ($stmt_update->execute()) {
            $current_stok = $this->stokBukuModel->getJumlahTersedia($peminjaman_data['id_buku']);
            $new_stok = $current_stok + 1;
            $this->stokBukuModel->updateStok($peminjaman_data['id_buku'], $new_stok);

            $this->addToHistory($id_peminjaman, $peminjaman_data['nim_mahasiswa'], $peminjaman_data['id_buku'], $peminjaman_data['tanggal_pinjam'], $tanggal_kembali_aktual);
            return true;
        }
        return false;
    }

    private function addToHistory($id_peminjaman, $nim_mahasiswa, $id_buku, $tanggal_pinjam, $tanggal_kembali) {
        $id_history = Utils::generateUuid();
        $date1 = new DateTime($tanggal_pinjam);
        $date2 = new DateTime($tanggal_kembali);
        $interval = $date1->diff($date2);
        $lama_pinjam_hari = $interval->days;

        $query = "INSERT INTO " . $this->table_history . "
                  SET id_history=:id_history, id_peminjaman=:id_peminjaman, nim_mahasiswa=:nim_mahasiswa,
                      id_buku=:id_buku, tanggal_pinjam=:tanggal_pinjam, tanggal_kembali=:tanggal_kembali,
                      lama_pinjam_hari=:lama_pinjam_hari";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_history", $id_history);
        $stmt->bindParam(":id_peminjaman", $id_peminjaman);
        $stmt->bindParam(":nim_mahasiswa", $nim_mahasiswa);
        $stmt->bindParam(":id_buku", $id_buku);
        $stmt->bindParam(":tanggal_pinjam", $tanggal_pinjam);
        $stmt->bindParam(":tanggal_kembali", $tanggal_kembali);
        $stmt->bindParam(":lama_pinjam_hari", $lama_pinjam_hari);

        return $stmt->execute();
    }

    public function getActiveLoans() {
        $query = "SELECT tp.*, mb.judul AS nama_buku, mm.nama_mahasiswa
                  FROM " . $this->table_peminjaman . " tp
                  JOIN MasterBuku mb ON tp.id_buku = mb.id_buku
                  JOIN MasterMahasiswa mm ON tp.nim_mahasiswa = mm.nim
                  WHERE tp.status = 'dipinjam'
                  ORDER BY tp.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getHistory($filters = [], $sort = 'tanggal_pinjam DESC', $limit = 10, $offset = 0, $nim_mahasiswa = null) {
        $where = "WHERE 1=1 ";
        $params = [];

        if ($nim_mahasiswa && Auth::getRole() === 'mahasiswa') {
            $where .= "AND hp.nim_mahasiswa = :nim_mahasiswa ";
            $params[':nim_mahasiswa'] = $nim_mahasiswa;
        }

        if (!empty($filters['nim'])) {
            $where .= "AND hp.nim_mahasiswa LIKE :nim ";
            $params[':nim'] = '%' . $filters['nim'] . '%';
        }
        if (!empty($filters['nama_mahasiswa'])) {
            $where .= "AND mm.nama_mahasiswa LIKE :nama_mahasiswa ";
            $params[':nama_mahasiswa'] = '%' . $filters['nama_mahasiswa'] . '%';
        }
        if (!empty($filters['id_buku'])) {
            $where .= "AND hp.id_buku = :id_buku ";
            $params[':id_buku'] = $filters['id_buku'];
        }
        if (!empty($filters['nama_buku'])) {
            $where .= "AND mb.judul LIKE :nama_buku ";
            $params[':nama_buku'] = '%' . $filters['nama_buku'] . '%';
        }
        if (!empty($filters['tanggal_pinjam'])) {
            $where .= "AND hp.tanggal_pinjam = :tanggal_pinjam ";
            $params[':tanggal_pinjam'] = $filters['tanggal_pinjam'];
        }
        if (!empty($filters['tanggal_kembali'])) {
            $where .= "AND hp.tanggal_kembali = :tanggal_kembali ";
            $params[':tanggal_kembali'] = $filters['tanggal_kembali'];
        }
        if (!empty($filters['lama_pinjam'])) {
            $where .= "AND hp.lama_pinjam_hari = :lama_pinjam ";
            $params[':lama_pinjam'] = $filters['lama_pinjam'];
        }

        $query = "SELECT hp.*, mb.judul AS nama_buku, mm.nama_mahasiswa
                  FROM " . $this->table_history . " hp
                  JOIN MasterBuku mb ON hp.id_buku = mb.id_buku
                  JOIN MasterMahasiswa mm ON hp.nim_mahasiswa = mm.nim
                  " . $where . "
                  ORDER BY " . $sort . "
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    public function countHistory($filters = [], $nim_mahasiswa = null) {
        $where = "WHERE 1=1 ";
        $params = [];

        if ($nim_mahasiswa && Auth::getRole() === 'mahasiswa') {
            $where .= "AND hp.nim_mahasiswa = :nim_mahasiswa ";
            $params[':nim_mahasiswa'] = $nim_mahasiswa;
        }

        if (!empty($filters['nim'])) {
            $where .= "AND hp.nim_mahasiswa LIKE :nim ";
            $params[':nim'] = '%' . $filters['nim'] . '%';
        }
        if (!empty($filters['nama_mahasiswa'])) {
            $where .= "AND mm.nama_mahasiswa LIKE :nama_mahasiswa ";
            $params[':nama_mahasiswa'] = '%' . $filters['nama_mahasiswa'] . '%';
        }
        if (!empty($filters['id_buku'])) {
            $where .= "AND hp.id_buku = :id_buku ";
            $params[':id_buku'] = $filters['id_buku'];
        }
        if (!empty($filters['nama_buku'])) {
            $where .= "AND mb.judul LIKE :nama_buku ";
            $params[':nama_buku'] = '%' . $filters['nama_buku'] . '%';
        }
        if (!empty($filters['tanggal_pinjam'])) {
            $where .= "AND hp.tanggal_pinjam = :tanggal_pinjam ";
            $params[':tanggal_pinjam'] = $filters['tanggal_pinjam'];
        }
        if (!empty($filters['tanggal_kembali'])) {
            $where .= "AND hp.tanggal_kembali = :tanggal_kembali ";
            $params[':tanggal_kembali'] = $filters['tanggal_kembali'];
        }
        if (!empty($filters['lama_pinjam'])) {
            $where .= "AND hp.lama_pinjam_hari = :lama_pinjam ";
            $params[':lama_pinjam'] = $filters['lama_pinjam'];
        }

        $query = "SELECT COUNT(*) as total_rows
                  FROM " . $this->table_history . " hp
                  JOIN MasterBuku mb ON hp.id_buku = mb.id_buku
                  JOIN MasterMahasiswa mm ON hp.nim_mahasiswa = mm.nim
                  " . $where;

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

}
