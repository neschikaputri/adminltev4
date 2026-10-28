<?php

include "koneksi.php";

// Mengambil NIM dari URL
$nim = $_GET['nim'];

// Mengambil data berdasarkan NIM
$sql = "SELECT * FROM nilai_mhsw WHERE nim = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $nim);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$data) {
    die("Data tidak ditemukan");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Nilai Mahasiswa</title>
</head>

<body>

    <h2>Edit Nilai Mahasiswa</h2>

    <form action="update_nilai.php" method="POST">

        <!-- NIM -->
        <label>NIM:</label><br>

        <input type="text"
               name="nim"
               value="<?php echo htmlspecialchars($data['nim']); ?>"
               readonly>

        <br><br>


        <!-- Nama -->
        <label>Nama Lengkap:</label><br>

        <input type="text"
               name="nama_lengkap"
               value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>"
               required>

        <br><br>


        <!-- Nilai -->
        <label>Nilai Akhir:</label><br>

        <input type="number"
               name="nilai_akhir"
               value="<?php echo htmlspecialchars($data['nilai_akhir']); ?>"
               min="0"
               max="100"
               step="0.01"
               required>

        <br><br>

        <button type="submit">
            Update
        </button>

        <button type="button"
                onclick="window.location.href='bacadata.php'">
            Batal
        </button>

    </form>

</body>
</html>