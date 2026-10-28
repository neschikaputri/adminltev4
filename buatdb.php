<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "<h2>Connected successfully</h2>";

// Create database
$sql = "CREATE DATABASE db5d";
if (mysqli_query($conn, $sql)) {
  echo "Database <strong>db5d</strong> created successfully";
} else {
  echo "Error creating database: " . mysqli_error($conn);
}

// Close connection
$conn->close();
?>