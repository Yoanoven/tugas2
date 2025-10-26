<?php
$conn = new mysqli("localhost", "root", "", "anggota", 3306);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika tombol "Simpan" ditekan (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_mhs = intval($_POST['id_mhs']);
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $prodi = $_POST['prodi'];
    $catatan = $_POST['catatan'];
    $minat = isset($_POST['minat']) ? $_POST['minat'] : [];

    // Ambil id_prodi dari kode_prodi
    $stmt_prodi = $conn->prepare("SELECT id_prodi FROM prodi WHERE kode_prodi = ?");
    $stmt_prodi->bind_param("s", $prodi);
    $stmt_prodi->execute();
    $result_prodi = $stmt_prodi->get_result();
    $row_prodi = $result_prodi->fetch_assoc();
    $id_prodi = $row_prodi ? $row_prodi['id_prodi'] : null;

    // Update data mahasiswa
    $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, jk=?, id_prodi=?, catatan=? WHERE id_mhs=?");
    $stmt->bind_param("ssisi", $nama, $jk, $id_prodi, $catatan, $id_mhs);
    $stmt->execute();

    // Hapus minat lama
    $conn->query("DELETE FROM mahasiswa_minat WHERE id_mhs = $id_mhs");

    // Tambahkan minat baru
    $stmt_minat = $conn->prepare("SELECT id_minat FROM minat WHERE kode_minat = ?");
    $stmt_insert = $conn->prepare("INSERT INTO mahasiswa_minat (id_mhs, id_minat) VALUES (?, ?)");

    foreach ($minat as $kode_minat) {
        $stmt_minat->bind_param("s", $kode_minat);
        $stmt_minat->execute();
        $result_minat = $stmt_minat->get_result();
        if ($row_minat = $result_minat->fetch_assoc()) {
            $id_minat = $row_minat['id_minat'];
            $stmt_insert->bind_param("ii", $id_mhs, $id_minat);
            $stmt_insert->execute();
        }
    }

    // Simpan log
    $aksi = "Edit data";
    $stmt_log = $conn->prepare("INSERT INTO log_pendaftaran (id_mhs, aksi) VALUES (?, ?)");
    $stmt_log->bind_param("is", $id_mhs, $aksi);
    $stmt_log->execute();

    header("Location: aksi.php");
    exit;
}

// Jika dibuka via GET (tombol edit)
if (isset($_GET['id'])) {
    $id_mhs = intval($_GET['id']);

    // Ambil data mahasiswa
    $stmt = $conn->prepare("
        SELECT m.*, p.kode_prodi 
        FROM mahasiswa m 
        LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
        WHERE m.id_mhs = ?
    ");
    $stmt->bind_param("i", $id_mhs);
    $stmt->execute();
    $result = $stmt->get_result();
    $mhs = $result->fetch_assoc();

    if (!$mhs) {
        echo "Data mahasiswa tidak ditemukan.";
        exit;
    }

    // Ambil daftar prodi
    $prodi_result = $conn->query("SELECT * FROM prodi");

    // Ambil daftar minat
    $minat_result = $conn->query("SELECT * FROM minat");

    // Ambil minat yang sudah dipilih
    $minat_terpilih = [];
    $res_minat_mhs = $conn->query("SELECT id_minat FROM mahasiswa_minat WHERE id_mhs = $id_mhs");
    while ($row = $res_minat_mhs->fetch_assoc()) {
        $minat_terpilih[] = $row['id_minat'];
    }
} else {
    echo "Akses tidak sah.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            background-color: #2c3e50;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        .container {
            max-width: 720px;
            margin: 48px auto;
            padding: 20px;
            background: #fbfbfb;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }

        h2 {
            margin-bottom: 18px;
            font-size: 1.6rem;
            color: #222;
        }

        .form-row {
            margin-bottom: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.95rem;
        }

        input[type="text"],
        select,
        textarea {
            padding: 10px 12px;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            font-size: 0.95rem;
            background: #fff;
            outline: none;
        }

        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            border-color: #6b8cff;
            box-shadow: 0 0 0 4px rgba(107,140,255,0.08);
        }

        fieldset {
            border: 1px solid #e6e6e6;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        legend {
            font-size: 0.95rem;
            padding: 0 6px;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            background: #3461ff;
            color: #fff;
        }

        .btn:hover {
            background: #274bd3;
        }

        .btn-back {
            background: #f0f0f0;
            color: #222;
            margin-left: 8px;
        }

        .btn-back:hover {
            background: #e0e0e0;
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
<main class="container">
    <h2>Edit Data Mahasiswa</h2>
    <form action="edit.php" method="POST">
        <input type="hidden" name="id_mhs" value="<?= $mhs['id_mhs'] ?>">

        <div class="form-row">
            <label>Nama:</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($mhs['nama']) ?>" required>
        </div>

        <div class="form-row">
            <label>Jenis Kelamin:</label>
            <select name="jk" required>
            <option value="Laki-laki" <?= $mhs['jk'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= $mhs['jk'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            <option value="Lainnya" <?= $mhs['jk'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
        </select>

        </div>

        <div class="form-row">
            <label>Program Studi:</label>
            <select name="prodi" required>
                <?php while ($p = $prodi_result->fetch_assoc()) { ?>
                    <option value="<?= $p['kode_prodi'] ?>" <?= $p['id_prodi'] == $mhs['id_prodi'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nama_prodi']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <fieldset>
            <legend>Minat:</legend>
            <?php while ($min = $minat_result->fetch_assoc()) { ?>
                <label>
                    <input type="checkbox" name="minat[]" value="<?= $min['kode_minat'] ?>"
                        <?= in_array($min['id_minat'], $minat_terpilih) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($min['nama_minat']) ?>
                </label>
            <?php } ?>
        </fieldset>

        <div class="form-row">
            <label>Catatan:</label>
            <textarea name="catatan"><?= htmlspecialchars($mhs['catatan']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-edit">Simpan Perubahan</button>
        <a href="aksi.php" class="btn btn-back">Kembali</a>
    </form>
</main>
</body>
</html>
