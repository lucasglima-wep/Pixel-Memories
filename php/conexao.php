<?php
ob_start("ob_gzhandler");
// ... resto do seu código
$host = "db"; 
$user = "root"; 
$pass = "senha_pixel"; // A senha deve ser a mesma do docker-compose
$db   = "pixel_memories"; 
$port = 3306;

// Criando a conexão
$conn = new mysqli($host, $user, $pass, $db, $port);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>