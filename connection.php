<?php
$hostname = "localhost";
$port = 3306;
$username_database = "root";
$password_database = "";
$database = "anggota";

$connection = new mysqli($hostname, $username_database, $password_database, $database, $port);

// Cek koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

// Ambil data dari form
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$jk = isset($_POST['jk']) ? $_POST['jk'] : '';
$prodi = isset($_POST['prodi']) ? $_POST['prodi'] : '';
$minat = isset($_POST['minat']) ? $_POST['minat'] : []; // pastikan array
if (!is_array($minat)) {
    $minat = [$minat];
}
$catatan = isset($_POST['catatan']) ? $_POST['catatan'] : '';


// 1️⃣ Simpan ke tabel mahasiswa
$sql = "INSERT INTO mahasiswa (nama, jk, id_prodi, catatan) VALUES (?, ?, ?, ?)";
$stmt = $connection->prepare($sql);

if ($stmt === false) {
    die("Gagal menyiapkan statement: " . $connection->error);
}

// Konversi prodi (misalnya value di form 'ti', 'si', 'te' ke ID prodi di DB)
$stmt_prodi = $connection->prepare("SELECT id_prodi FROM prodi WHERE kode_prodi = ?");
$stmt_prodi->bind_param("s", $prodi);
$stmt_prodi->execute();
$result_prodi = $stmt_prodi->get_result();

if ($result_prodi->num_rows > 0) {
    $row_prodi = $result_prodi->fetch_assoc();
    $id_prodi = $row_prodi['id_prodi'];
} else {
    $id_prodi = null;
}

$stmt->bind_param("ssis", $nama, $jk, $id_prodi, $catatan);

if ($stmt->execute()) {
    echo "<h3>Data mahasiswa berhasil disimpan.</h3>";
} else {
    die("Error insert mahasiswa: " . $stmt->error);
}

$id_mhs = $connection->insert_id; // ambil ID mahasiswa terakhir

// Simpan relasi minat ke tabel mahasiswa_minat
$stmt_minat = $connection->prepare("SELECT id_minat FROM minat WHERE kode_minat = ?");
$stmt_insert_minat = $connection->prepare("INSERT INTO mahasiswa_minat (id_mhs, id_minat) VALUES (?, ?)");

foreach ($minat as $kode) {
    $stmt_minat->bind_param("s", $kode);
    $stmt_minat->execute();
    $result_minat = $stmt_minat->get_result();

    if ($result_minat->num_rows > 0) {
        $row_minat = $result_minat->fetch_assoc();
        $id_minat = $row_minat['id_minat'];

        $stmt_insert_minat->bind_param("ii", $id_mhs, $id_minat);
        $stmt_insert_minat->execute();
    }
}

$aksi = "Pendaftaran baru";
    $sql_log = "INSERT INTO log_pendaftaran (id_mhs, aksi) VALUES (?, ?)";
    $stmt_log = $connection->prepare($sql_log);
    $stmt_log->bind_param("is", $id_mhs, $aksi);
    $stmt_log->execute();
    $stmt_log->close();

    // Commit transaksi
    $connection->commit();

echo "<a href='form.php'>Kembali ke Form</a>";

$stmt->close();
$connection->close();
?>
