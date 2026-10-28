<?php
// update_nilai.php - simpan perubahan (dari modal / form Edit Data Nilai)
include "koneksi.php";
include "fungsi_nilai.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adminlte.php");
    exit;
}

$nim   = trim($_POST['nim'] ?? '');
$nama  = trim($_POST['nama_lengkap'] ?? '');
$nilai = $_POST['nilai_akhir'] ?? '';

if ($nim === '' || $nama === '' || !is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("Data tidak valid."));
    exit;
}
$nilai = (float)$nilai;

list($huruf, $predikat) = hitung_nilai($nilai);

$sql  = "UPDATE nilai_mhsw
         SET nama_lengkap = ?, nilai_akhir = ?, nilai_huruf = ?, predikat = ?
         WHERE nim = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query gagal disiapkan: " . $conn->error);
}

$stmt->bind_param("sdsss", $nama, $nilai, $huruf, $predikat, $nim);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: adminlte.php?tipe=success&msg=" . urlencode("Data nilai berhasil diperbarui."));
    exit;
} else {
    echo "Data gagal diperbarui: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>