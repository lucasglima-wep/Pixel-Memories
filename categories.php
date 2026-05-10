<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.html");
    exit();
}

include "php/conexao.php";

/* =========================
   CRIAR CATEGORIA
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome !== '' && $descricao !== '') {

        $imagem = "fotos/default.jpg";

        /* 📸 UPLOAD */
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','webp'];

            if (in_array($ext, $permitidos)) {

                $pasta = "fotos/";

                if (!is_dir($pasta)) {
                    mkdir($pasta, 0777, true);
                }

                $tmp = $_FILES['imagem']['tmp_name'];

                /* nome único (mantém extensão correta) */
                $novo_nome = uniqid("cat_") . "." . $ext;

                $caminho_final = $pasta . $novo_nome;

                /* criar imagem */
                switch ($ext) {

                    case 'jpg':
                    case 'jpeg':
                        $img = imagecreatefromjpeg($tmp);
                        break;

                    case 'png':
                        $img = imagecreatefrompng($tmp);
                        imagepalettetotruecolor($img);
                        break;

                    case 'webp':
                        $img = imagecreatefromwebp($tmp);
                        break;

                    default:
                        $img = null;
                }

                /* salvar */
                if ($img) {

                    imagejpeg($img, $caminho_final, 75);
                    imagedestroy($img);

                    $imagem = $caminho_final;
                }
            }
        }

        /* 💾 SALVAR BANCO */
        $sql = "INSERT INTO categorias (nome, descricao, imagem)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $descricao, $imagem);
        $stmt->execute();

        header("Location: categories.php?status=sucesso");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Categorias - Pixel Memories</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

body {
    padding: 80px;
    margin: 0;
}

.categoria-box {
    width: 90%;
    max-width: 700px;
    margin: 40px auto;
    background: #1a1a1a;
    padding: 40px;
    border-radius: 20px;
}

.categoria-box h2 {
    color: #fff;
    margin-bottom: 20px;
}

.categoria-box form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.categoria-box input,
.categoria-box textarea {
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #2a2a2a;
    color: #fff;
}

.categoria-box button {
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #4f7cff;
    color: #fff;
    cursor: pointer;
    font-weight: bold;
}

.lista-categorias {
    margin-top: 40px;
}

.item-categoria {
    background: #222;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 15px;
    color: #fff;
}

.item-categoria h3 {
    margin-bottom: 8px;
}

.btn-excluir {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 12px;
    background: #ff4d4d;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
}

.btn-excluir:hover {
    background: #ff2d2d;
}

</style>

</head>
<body>

<div id="loading">

    <div class="loader-box">

        <div class="spinner"></div>

        <h2>criando categoria...</h2>

        <p>Aguarde um momento</p>

    </div>

</div>

<header class="header">

    <div class="logo">
        <i class="fa-solid fa-layer-group"></i>
        <h1>Categorias</h1>
    </div>

    <nav class="menu">
        <a href="index.php">Início</a>
        <a href="galeria.php">Galeria</a>
        <a href="admin.php">Admin</a>
        <a href="categories.php" class="ativo">Categorias</a>
    </nav>

</header>

<section class="categoria-box">

    <h2>
        <i class="fa-solid fa-folder-plus"></i>
        Nova Categoria
    </h2>

    <form method="POST" enctype="multipart/form-data">

    <input type="text" name="nome" placeholder="Nome da categoria" required>

    <textarea name="descricao" rows="4" placeholder="Descrição da categoria" required></textarea>

    <label>Imagem da categoria</label>
    <input type="file" name="imagem" accept="image/*" required>

    <button type="submit">Criar Categoria</button>

</form>

    <div class="lista-categorias">

        <?php
        $sql = "SELECT * FROM categorias ORDER BY id DESC";
        $resultado = $conn->query($sql);

        if ($resultado && $resultado->num_rows > 0) {

            while ($categoria = $resultado->fetch_assoc()) {
        ?>

        <div class="item-categoria">

            <h3><?php echo htmlspecialchars($categoria['nome']); ?></h3>

            <p><?php echo htmlspecialchars($categoria['descricao']); ?></p>

            <a href="php/excluir_categoria.php?id=<?php echo $categoria['id']; ?>"
               class="btn-excluir"
               onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">

                <i class="fa-solid fa-trash"></i> Excluir

            </a>

        </div>

        <?php
            }
        } else {
            echo "<p style='color:#aaa;'>Nenhuma categoria cadastrada.</p>";
        }
        ?>

    </div>

</section>

<script>
    const formUpload = document.getElementById('form-upload');
    const loading = document.getElementById('loading');

    if(formUpload){

        formUpload.addEventListener('submit', (e) => {

    e.preventDefault();

    loading.style.display = 'flex';

    setTimeout(() => {

        formUpload.submit();

    }, 3000); // 2 segundos mínimos

});
    }

</script>

</body>
</html>