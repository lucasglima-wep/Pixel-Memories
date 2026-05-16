<?php
$host = "localhost";
$user = "root"; 
$pass = "root";     
$db   = "pixel_memories"; 
  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>