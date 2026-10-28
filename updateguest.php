<?php
require_once "koneksi.php";

// Ambil ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Jika form dikirim, proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validasi data
    if (
        !$id || $id < 1 ||
        $fname === '' ||
        $lname === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        die("Data tidak valid. Periksa kembali semua kolom.");
    }

    // SQL untuk memperbarui data
    $sql = "UPDATE MyGuests
            SET firstname = ?, lastname = ?, email = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Gagal menyiapkan query: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $fname,
        $lname,
        $email,
        $id
    );

    // Jalankan query update
    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // Kembali ke halaman daftar guest
        header("Location: bacadata2.php?status=updated");
        exit;

    } else {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        die("Gagal memperbarui data: " . $error);
    }
}

// Periksa ID untuk menampilkan form edit
if (!$id || $id < 1) {
    mysqli_close($conn);
    die("ID guest tidak valid.");
}

// Ambil data berdasarkan ID
$sql = "SELECT * FROM MyGuests WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$row) {
    mysqli_close($conn);
    die("Data guest tidak ditemukan.");
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guest</title>
</head>
<body>

<h2>Edit My Guest</h2>

<form action="updateguest.php" method="POST">

    <input type="hidden" name="id"
           value="<?= (int)$row['id'] ?>">

    <label for="fname">First Name:</label><br>
    <input
        type="text"
        id="fname"
        name="fname"
        value="<?= htmlspecialchars($row['firstname'], ENT_QUOTES, 'UTF-8') ?>"
        required
    >
    <br><br>

    <label for="lname">Last Name:</label><br>
    <input
        type="text"
        id="lname"
        name="lname"
        value="<?= htmlspecialchars($row['lastname'], ENT_QUOTES, 'UTF-8') ?>"
        required
    >
    <br><br>

    <label for="email">Email:</label><br>
    <input
        type="email"
        id="email"
        name="email"
        value="<?= htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        required
    >
    <br><br>

    <button type="submit">Update</button>

    <button
        type="button"
        onclick="window.location.href='bacadata2.php'">
        Batal
    </button>

</form>

</body>
</html>