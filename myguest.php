<?php
require "koneksi.php";

// baca data dari kiriman form: form_guest.html
$fname = $_POST ['fname'];
$lname = $_POST ['lname'];
$email = $_POST ['email'];
// sql to create table
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('$fname', '$lname', '$email')";

if (mysqli_query($conn, $sql)) {
  echo "New record inserted successfully";
} else {
  echo "Error inserting record: " . mysqli_error($conn);
}

// kembali ke tampilan data guest
header('Location:bacadata2.php');

mysqli_close($conn);
?>