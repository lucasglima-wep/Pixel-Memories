<?php

include "conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método inválido");
}

$id = intval($_POST['id']);

$titulo = trim($_POST['titulo']);
$categoria = intval($_POST['categoria']); // <-- importante (INT)

$sql = "UPDATE fotos
        SET titulo = ?,
            categoria_id = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sii",
    $titulo,
    $categoria,
    $id
);

if ($stmt->execute()) {

    header("Location: ../admin.php?status=editado");
    exit();

} else {

    echo "Erro ao atualizar: " . $stmt->error;

    if ($stmt->execute()) {
        header("Location: ../admin.php?status=editado");
        exit();
    }
}
?>