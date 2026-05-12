<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Note o final da linha: ?erro=acesso_negado
    header("Location: login.html?erro=acesso_negado"); 
    exit();
}


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.html");
    exit();
}

include "php/conexao.php";

/* =========================
   CRIAR CATEGORIA
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_criar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome !== '' && $descricao !== '') {
        $imagem = "fotos/default.jpg";

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg','jpeg','png','webp'];

            if (in_array($ext, $permitidos)) {
                $pasta = "fotos/";
                if (!is_dir($pasta)) mkdir($pasta, 0777, true);
                $novo_nome = uniqid("cat_") . "." . $ext;
                $caminho_final = $pasta . $novo_nome;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_final)) {
                    $imagem = $caminho_final;
                }
            }
        }

        $sql = "INSERT INTO categorias (nome, descricao, imagem) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $descricao, $imagem);
        $stmt->execute();

        header("Location: categories.php?status=sucesso");
        exit();
    }
}

/* =========================
   EDITAR CATEGORIA
   ========================= */
if (isset($_POST['btn_editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $imagem = $_POST['imagem_atual'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid("cat_") . "." . $ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], "fotos/" . $novo_nome);
        $imagem = "fotos/" . $novo_nome;
    }

    $stmt = $conn->prepare("UPDATE categorias SET nome=?, descricao=?, imagem=? WHERE id=?");
    $stmt->bind_param("sssi", $nome, $descricao, $imagem, $id);
    $stmt->execute();
    header("Location: categories.php?status=editado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Pixel Memories</title>
    <link rel="icon" href="fotos/logo.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/categorias.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>


    </style>
</head>
<body>

<div id="alerta">
    <i class="fa-solid fa-circle-info"></i>
    <span id="alerta-mensagem"></span>
</div>

<header class="header">
    <div class="logo">
        <i class="fa-solid fa-layer-group"></i>
        <h1>Categorias</h1>
    </div>
    <nav class="menu">
        <a href="index.php">Início</a>
        <a href="galeria.php">Galeria</a>
        <a href="admin.php" >Admin</a>
        <a href="categories.php" class="ativo" >Categorias</a>
        <a href="php/logout.php" style="color:#ff6b6b;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Sair
        </a>
    </nav>

</header>

<section class="categoria-box">
    <h2><i class="fa-solid fa-folder-plus"></i> Nova Categoria</h2>
    <form id="form-upload" method="POST" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome da categoria" required>
        <textarea name="descricao" rows="4" placeholder="Descrição da categoria" required></textarea>
        <label>Imagem da categoria</label>
        <input type="file" name="imagem" accept="image/*" required>
        <button type="submit" name="btn_criar">Criar Categoria</button>
    </form>

    <div class="lista-categorias">
        <?php
        $sql = "SELECT * FROM categorias ORDER BY id DESC";
        $resultado = $conn->query($sql);
        if ($resultado && $resultado->num_rows > 0) {
            while ($categoria = $resultado->fetch_assoc()) { ?>
                <div class="item-categoria">
                    <img src="<?php echo htmlspecialchars($categoria['imagem']); ?>" class="img-categoria">
                    <div class="categoria-info">
                        <h3><?php echo htmlspecialchars($categoria['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($categoria['descricao']); ?></p>
                        <div class="acoes-categoria">
                            <button class="btn-editar" onclick="abrirModalEditar('<?php echo $categoria['id']; ?>', '<?php echo addslashes($categoria['nome']); ?>', '<?php echo addslashes($categoria['descricao']); ?>', '<?php echo $categoria['imagem']; ?>')">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </button>
                            <a href="php/excluir_categoria.php?id=<?php echo $categoria['id']; ?>" class="btn-excluir" onclick="return confirm('Excluir?')">
                                <i class="fa-solid fa-trash"></i> Excluir
                            </a>
                        </div>
                    </div>
                </div>
            <?php }
        } else { echo "<p style='color:#aaa;'>Nenhuma categoria cadastrada.</p>"; } ?>
    </div>
</section>

<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="fechar" onclick="fecharModal()">&times;</span>
        <h2><i class="fa-solid fa-pen-to-square"></i> Editar Categoria</h2>
        <form id="formEditar" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="imagem_atual" id="edit-imagem-atual">
            
            <label>Nome</label>
            <input type="text" name="nome" id="edit-nome" required>
            
            <label>Descrição</label>
            <textarea name="descricao" id="edit-descricao" rows="3" required style="width:100%; background:#2a2a2a; 
            color:white; border-radius:10px; padding:10px; border:none; margin-top:10px; outline:none;"></textarea>
            
            <label style="display:block; margin-top:15px;">Imagem Atual:</label>
            <img src="" id="preview-edit" style="width:80px; height:80px; border-radius:10px; margin:10px 0; object-fit: cover; border: 1px solid #4da3ff;">
            
            <label style="display:block;">Trocar Imagem (opcional)</label>
            <input type="file" name="imagem" accept="image/*">
            
            <button type="submit" name="btn_editar" style="background:#4da3ff; margin-top:20px;">Salvar Alterações</button>
        </form>
    </div>
</div>

<script>
// 1. FUNÇÕES DO MODAL (Escopo Global)
function abrirModalEditar(id, nome, descricao, imagem) {
    document.getElementById('modalEditar').style.display = 'flex';
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nome').value = nome;
    document.getElementById('edit-descricao').value = descricao;
    document.getElementById('edit-imagem-atual').value = imagem;
    document.getElementById('preview-edit').src = imagem;
}

function fecharModal() {
    document.getElementById('modalEditar').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('modalEditar');
    if (event.target == modal) fecharModal();
}

// 2. SISTEMA DE ALERTAS PREMIUM
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
const alerta = document.getElementById('alerta');
const mensagem = document.getElementById('alerta-mensagem');
const iconeAlerta = alerta.querySelector('i');

if (status) {
    // Garante que o container esteja pronto para a animação
    alerta.style.display = 'flex';
    
    const msgMap = {
        'sucesso': 'Categoria criada com sucesso!',
        'editado': 'Categoria atualizada!',
        'excluido': 'Categoria excluída!',
        'erro': 'Erro na operação.'
    };

    // Aplica a cor e o ícone baseado no status
    if (status === 'erro') {
        alerta.classList.add('alerta-erro');
        if(iconeAlerta) {
            iconeAlerta.className = 'fa-solid fa-circle-xmark';
            iconeAlerta.style.color = '#e74c3c';
        }
    } else {
        alerta.classList.add('alerta-sucesso');
        if(iconeAlerta) {
            iconeAlerta.className = 'fa-solid fa-circle-check';
            iconeAlerta.style.color = '#27ae60';
        }
    }

    mensagem.innerText = msgMap[status] || 'Operação realizada.';

    // Dispara a animação (Pop-in)
    setTimeout(() => {
        alerta.classList.add('active');
    }, 50);

    // Fade-out e fechar após 4 segundos
    setTimeout(() => {
        alerta.classList.remove('active');
        setTimeout(() => {
            alerta.style.display = 'none';
            // Remove as classes de cor para a próxima vez
            alerta.classList.remove('alerta-sucesso', 'alerta-erro');
        }, 500);
    }, 4000);
}
</script>

</body>
</html>