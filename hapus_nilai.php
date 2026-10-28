<?php
// hapus_nilai.php - dipanggil dari tombol hapus di adminlte.php (POST)
include "koneksi.php";

// Pastikan data dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adminlte.php");
    exit;
}

$nim = trim($_POST['nim'] ?? '');

if ($nim === '') {
    die("NIM tidak ditemukan.");
}

$sql  = "DELETE FROM nilai_mhsw WHERE nim = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . $conn->error);
}

$stmt->bind_param("s", $nim);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: adminlte.php?tipe=success&msg=" . urlencode("Data nilai berhasil dihapus."));
    exit;
} else {
    echo "Data gagal dihapus: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>