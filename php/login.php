<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include "conexao.php";

/* 1. VERIFICA SE OS CAMPOS ESTÃO VAZIOS */
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    header("Location: ../login.html?erro=campos");
    exit();
}

/* PEGAR DADOS E PROTEGER */
$usuario = $conn->real_escape_string($_POST['usuario']);
$senha = $conn->real_escape_string($_POST['senha']);

/* 2. BUSCA APENAS PELO USUÁRIO PRIMEIRO */
$sql = "SELECT * FROM admins WHERE usuario='$usuario'";
$result = $conn->query($sql);

/* 3. VERIFICA SE O USUÁRIO EXISTE */
if($result && $result->num_rows > 0){
    
    $dados = $result->fetch_assoc();

    /* 4. VERIFICA SE A SENHA CONFERE */
    if($senha === $dados['senha']){
        // Sucesso: Cria a sessão
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;

        header("Location: ../admin.php");
        exit();
    } else {
        // Erro: Senha incorreta
        header("Location: ../login.html?erro=senha");
        exit();
    }

} else {
    // Erro: Usuário não encontrado no banco
    header("Location: ../login.html?erro=usuario");
    exit();
}
?>