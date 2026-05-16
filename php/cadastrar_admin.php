<?php
session_start();
include "conexao.php";

/*  valida campos */
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    header("Location: ../cadastro_admin.php?erro=campos");
    exit();
}

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

/*  verifica se já existe usuário */
$stmt = $conn->prepare("SELECT id FROM admins WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: ../cadastro_admin.php?erro=existente");
    exit();
}

/*  criptografa senha */
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

/*  insere no banco */
$stmt = $conn->prepare("INSERT INTO admins (usuario, senha) VALUES (?, ?)");
$stmt->bind_param("ss", $usuario, $senhaHash);

if ($stmt->execute()) {
    header("Location: ../login.html?status=admin_criado");
    exit();
} else {
    header("Location: ../cadastro_admin.php?erro=banco");
    exit();
}
?>