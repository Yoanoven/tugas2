<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota Komunitas</title>
    <link rel="stylesheet" href="aksi.css">
</head>
<body>
    <header class="navbar">
        <nav>
            <a href="123240264main.html">Home</a>
            <a href="form.php">Form</a>
            <a href="aksi.php" class="active">Data Anggota</a>
        </nav>
    </header>

    <main class="container">
        <h1>Data Anggota Komunitas</h1>

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Program Studi</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Koneksi database
                $conn = new mysqli("localhost", "root", "", "anggota", 3306);
                if ($conn->connect_error) {
                    die("Koneksi gagal: " . $conn->connect_error);
                }

                // Ambil data mahasiswa + prodi + minat
                $sql = "
                SELECT 
                m.id_mhs,
                m.nama, 
                m.jk, 
                p.nama_prodi,
                m.catatan
                FROM mahasiswa m
                LEFT JOIN prodi p ON m.id_prodi = p.id_prodi
                GROUP BY m.id_mhs;
                ";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars(ucfirst($row['jk'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_prodi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['catatan']) . "</td>";
                        echo "<td>
                                <div class='aksi'>
                                    <a href='edit.php?id=" . $row['id_mhs'] . "' class='btn btn-edit'>Edit</a>
                                    <a href='delete.php?id=" . $row['id_mhs'] . "' class='btn btn-delete'>Delete</a>
                                </div>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='kosong'>Belum ada data anggota</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="form.php" class="btn-add">Tambah Anggota Baru</a>
    </main>
</body>
</html>

