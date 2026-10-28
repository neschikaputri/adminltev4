<?php

include "koneksi.php";

// Pastikan data dikirim lewat form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adminlte.php");
    exit;
}

// Ambil data dari form  
$nim          = trim($_POST['nim']);
$nama_lengkap = trim($_POST['nama_lengkap']);
$nilai_akhir  = $_POST['nilai_akhir'];

// Menentukan nilai huruf dan predikat
if ($nilai_akhir >= 0 && $nilai_akhir <= 34.50) {
    $nilai_huruf = "E";
    $predikat = "Tidak Lulus";
} elseif ($nilai_akhir >= 34.51 && $nilai_akhir <= 50.50) {
    $nilai_huruf = "D";
    $predikat = "Kurang Memuaskan";
} elseif ($nilai_akhir >= 50.51 && $nilai_akhir <= 65.50) {
    $nilai_huruf = "C";
    $predikat = "Cukup Memuaskan";
} elseif ($nilai_akhir >= 65.51 && $nilai_akhir <= 80.50) {
    $nilai_huruf = "B";
    $predikat = "Memuaskan";
} elseif ($nilai_akhir >= 80.51 && $nilai_akhir <= 100) {
    $nilai_huruf = "A";
    $predikat = "Sangat Memuaskan";
} else {
    die("Invalid value. Silahkan masukkan nilai antara 0 dan 100.");
}

// Cek NIM sudah ada atau belum
$cek = $conn->prepare("SELECT nim FROM nilai_mhsw WHERE nim = ?");
$cek->bind_param("s", $nim);
$cek->execute();
$cek->store_result();
if ($cek->num_rows > 0) {
    $cek->close();
    $conn->close();
    header("Location: adminlte.php?tipe=danger&msg=" . urlencode("NIM sudah terdaftar."));
    exit;
}
$cek->close();

// Simpan ke database (pakai tanda ? supaya aman dan tidak salah kutip)
$sql = "INSERT INTO nilai_mhsw
        (nim, nama_lengkap, nilai_akhir, nilai_huruf, predikat, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdss",
    $nim,
    $nama_lengkap,
    $nilai_akhir,
    $nilai_huruf,
    $predikat
);

// Jalankan query
if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: adminlte.php?tipe=success&msg=" . urlencode("Data nilai berhasil ditambahkan."));
    exit;

} else {

    echo "Data gagal disimpan: " . $stmt->error;

}

$stmt->close();
$conn->close();

?>