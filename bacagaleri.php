<?php
require_once __DIR__ . "/phpdb/koneksi.php";

// tombol untuk Tambah Foto Galeri
echo "<a href='form_upload.php'><button>Tambah Foto</button></a>";

$sql = "SELECT * FROM galeri";
// Execute the SQL query
$result = mysqli_query($conn, $sql)





echo "<caption><strong>My Photo Gallery</strong></caption>";
echo "<tr>"
        <th>No</th>
        <th>Nama File</th>
        <th>Tgl Unggah</th>
        <th>Pratinjau</th>
        <th>Aksi</th>
      </tr>";
      
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>".$row["id"]."</td>
            <td>".$row["namafile"]."</td>
            <td>".$row["tglunggah"]."</td>
            <td><img src=".$row["namafile"]." width='50'heigh</td>

