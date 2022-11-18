<?php
$servername = "172.16.1.69";
$username = "root";
$password = "NT3Movicel";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>