<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../login.html");
    exit();
}

include "conexao.php";

if (!isset($_GET['id'])) {
    header("Location: ../categories.php?erro=id");
    exit();
}

$id = intval($_GET['id']);

/* 1. BUSCAR O NOME DA IMAGEM ANTES DE DELETAR O REGISTRO */
$sqlBusca = "SELECT imagem FROM categorias WHERE id = ?";
$stmtBusca = $conn->prepare($sqlBusca);
$stmtBusca->bind_param("i", $id);
$stmtBusca->execute();
$resultado = $stmtBusca->get_result();

if ($resultado->num_rows > 0) {
    $categoria = $resultado->fetch_assoc();
    $nomeImagem = $categoria['imagem'];

    // Define o caminho da pasta onde as fotos ficam (ajuste se necessário)
    // basename garante que pegamos apenas o arquivo, evitando caminhos duplicados
    $caminhoPasta = dirname(__DIR__) . DIRECTORY_SEPARATOR . "fotos" . DIRECTORY_SEPARATOR;
    $caminhoCompleto = $caminhoPasta . basename($nomeImagem);

    /* 2. DELETA O ARQUIVO FÍSICO */
    if (!empty($nomeImagem) && file_exists($caminhoCompleto)) {
        unlink($caminhoCompleto);
    }
}

/* 3. AGORA SIM, DELETA DO BANCO DE DADOS */
$sql = "DELETE FROM categorias WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../categories.php?status=excluido");
    exit();
} else {
    header("Location: ../categories.php?erro=bd");
    exit();
}
?>