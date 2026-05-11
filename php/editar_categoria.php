<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../login.html");
    exit();
}

include "conexao.php";

// 1. BUSCAR DADOS ATUAIS
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $categoria = $resultado->fetch_assoc();

    if (!$categoria) {
        die("Categoria não encontrada.");
    }
}

// 2. PROCESSAR A ATUALIZAÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $imagem_final = $_POST['imagem_atual']; // Mantém a antiga por padrão

    // Se o usuário enviou uma imagem nova
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $novo_nome = uniqid("cat_") . "." . $ext;
        $destino = "fotos/" . $novo_nome;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $imagem_final = $destino; // Atualiza para o novo caminho
        }
    }

    $sql = "UPDATE categorias SET nome = ?, descricao = ?, imagem = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $descricao, $imagem_final, $id);
    
    if ($stmt->execute()) {
        header("Location: ../categories.php?status=editado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoria - Pixel Memories</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .preview-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin: 10px 0;
            border: 2px solid #4da3ff;
        }
        .container-editar {
            max-width: 600px;
            margin: 50px auto;
            background: #1a1a1a;
            padding: 30px;
            border-radius: 20px;
            color: white;
        }
    </style>
</head>
<body>

<div class="container-editar">
    <h2><i class="fa-solid fa-pen-to-square"></i> Editar Categoria</h2>
    <p>Altere as informações abaixo:</p>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
        <input type="hidden" name="imagem_atual" value="<?php echo $categoria['imagem']; ?>">

        <label>Nome da Categoria</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>

        <label>Descrição</label>
        <textarea name="descricao" rows="4" required><?php echo htmlspecialchars($categoria['descricao']); ?></textarea>

        <label>Imagem Atual:</label><br>
        <img src="<?php echo $categoria['imagem']; ?>" class="preview-img"><br>

        <label>Trocar Imagem (opcional)</label>
        <input type="file" name="imagem" accept="image/*">

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" style="flex: 2; background: #2ecc71;">Salvar Alterações</button>
            <a href="categories.php" style="flex: 1; background: #555; text-align:center; padding: 14px; border-radius:10px; text-decoration:none; color:white; font-weight:bold;">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>