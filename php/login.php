<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "conexao.php";

/* VALIDA CAMPOS */
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    header("Location: ../login.html?erro=campos");
    exit();
}

/* BUSCA USUÁRIO */
$stmt = $conn->prepare("SELECT id, usuario, senha FROM admins WHERE usuario = ?");
$stmt->bind_param("s", $_POST['usuario']);
$stmt->execute();

$result = $stmt->get_result();

/* USUÁRIO NÃO EXISTE */
if ($result->num_rows === 0) {
    header("Location: ../login.html?erro=usuario");
    exit();
}

$dados = $result->fetch_assoc();

/* VERIFICA SENHA */
if (password_verify($_POST['senha'], $dados['senha'])) {

    session_regenerate_id(true);

    $_SESSION['logado'] = true;
    $_SESSION['usuario'] = $dados['usuario'];
    $_SESSION['id'] = $dados['id'];

    header("Location: ../admin.php");
    exit();

} else {

    header("Location: ../login.html?erro=senha");
    exit();

}

?>