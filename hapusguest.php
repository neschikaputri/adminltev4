<?php
require_once "koneksi.php";

// Pastikan permintaan berasal dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: bacadata2.php");
    exit;
}

// Ambil ID data
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    mysqli_close($conn);
    header("Location: bacadata2.php");
    exit;
}

// SQL untuk menghapus data
$sql = "DELETE FROM MyGuests WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $deleted = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if ($deleted > 0) {
        header("Location: bacadata2.php?status=deleted");
    } else {
        header("Location: bacadata2.php?status=notfound");
    }
    exit;
}

echo "Error deleting record: " . mysqli_stmt_error($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>