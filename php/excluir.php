<?php

include "conexao.php";

if (!isset($_GET['id'])) {
    die("ID inválido");
}

$id = intval($_GET['id']);

/* 1. BUSCA O ARQUIVO NO BANCO PARA SABER O NOME */
$sql = "SELECT caminho_arquivo FROM fotos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Foto não encontrada no banco de dados.");
}

$foto = $resultado->fetch_assoc();

/* 2. CONFIGURA O CAMINHO REAL DO ARQUIVO */
// dirname(__DIR__) sobe um nível da pasta atual (ex: de /admin para /raiz)
$diretorioRaiz = dirname(__DIR__); 
$nomeArquivo = basename($foto['caminho_arquivo']); // Pega apenas 'foto.jpg', ignora pastas gravadas no banco
$caminhoFinal = $diretorioRaiz . DIRECTORY_SEPARATOR . "fotos" . DIRECTORY_SEPARATOR . $nomeArquivo;

/* 3. DELETA O ARQUIVO FÍSICO */
if (!empty($nomeArquivo) && file_exists($caminhoFinal)) {
    if (!unlink($caminhoFinal)) {
        // Se o arquivo existe mas o PHP não conseguiu apagar (permissão)
        die("Erro: O servidor não tem permissão para excluir o arquivo na pasta.");
    }
} 
// Caso o arquivo não exista fisicamente, o código apenas segue para limpar o banco.

/* 4. DELETA O REGISTRO DO BANCO DE DADOS */
$sqlDelete = "DELETE FROM fotos WHERE id = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("i", $id);

if ($stmtDelete->execute()) {
    // Sucesso total! Redireciona para a página administrativa
    header("Location: ../admin.php?status=excluido");
    exit();
} else {
    echo "Erro ao excluir o registro do banco de dados.";
}

?>