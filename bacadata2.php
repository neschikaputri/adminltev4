<?php
require_once "koneksi.php";

$sql = "SELECT * FROM MyGuests";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>My Guests List</title>
</head>
<body>

<button onclick="window.location.href='form_guest.html'">
    Add My Guest
</button>

<br><br>

    <table border= 1 border-collapse='collapse'>
    <caption><strong>My Guests List</strong></caption>

    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Reg Date</th>
        <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>

                <td>
                    <?= htmlspecialchars(
                        $row['firstname'] . " " . $row['lastname']
                    ) ?>
                </td>

                <td><?= htmlspecialchars($row['email'] ?? '') ?></td>

                <td><?= htmlspecialchars($row['reg_date']) ?></td>

                <td>
                    <!-- Tombol Edit -->
                    
                    <form action="updateguest.php" method="GET"
                        style="display:inline">
                        <input type="hidden" name="id"
                            value="<?= (int)$row['id'] ?>">
                        <button type="submit">Edit</button>
                    </form>


                    <!-- Tombol Delete -->
                    <form action="hapusguest.php" method="POST"
                          style="display:inline"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        <input type="hidden" name="id"
                               value="<?= (int)$row['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

    <?php } else { ?>
        <tr>
            <td colspan="5">Data guest belum tersedia.</td>
        </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
mysqli_free_result($result);
mysqli_close($conn);
?>