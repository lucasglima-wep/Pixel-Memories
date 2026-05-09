<?php

include "conexao.php";

if (!isset($_GET['id'])) {
    die("ID inválido");
}

$id = intval($_GET['id']);

/* BUSCA O ARQUIVO */
$sql = "SELECT caminho_arquivo FROM fotos WHERE id = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Foto não encontrada");
}

$foto = $resultado->fetch_assoc();

/* CAMINHO DO ARQUIVO */
$caminho = "../fotos/" . $foto['caminho_arquivo'];

/* DELETA ARQUIVO FÍSICO */
if (!empty($foto['caminho_arquivo']) && file_exists($caminho)) {

    if (!unlink($caminho)) {
        die("Erro ao excluir arquivo da pasta.");
    }

}

/* DELETA DO BANCO */
$sqlDelete = "DELETE FROM fotos WHERE id = ?";
$stmtDelete = $conn->prepare($sqlDelete);

$stmtDelete->bind_param("i", $id);

if ($stmtDelete->execute()) {

    header("Location: ../admin.php?status=excluido");
    exit();

} else {

    echo "Erro ao excluir do banco.";

}

?>