<?php

include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    die("Método inválido");

}

$id = intval($_POST['id']);

$titulo = trim($_POST['titulo']);
$categoria = trim($_POST['categoria']);

$sql = "UPDATE fotos
        SET titulo = ?,
            categoria = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssi",
    $titulo,
    $categoria,
    $id
);

if ($stmt->execute()) {

    header("Location: ../admin.php");
    exit();

} else {

    echo "Erro ao atualizar.";

}

?>