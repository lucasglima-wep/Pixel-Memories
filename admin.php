<?php
session_start();

if (
    !isset($_SESSION['logado']) ||
    $_SESSION['logado'] !== true ||
    !isset($_SESSION['id'])
) {
    header("Location: login.html?erro=acesso_negado");
    exit();
}

/* CONEXÃO */
include "php/conexao.php";

/* CONTADOR */
$sqlCount = "SELECT COUNT(*) AS total FROM fotos";
$resultCount = $conn->query($sqlCount);
$totalFotos = ($resultCount) ? $resultCount->fetch_assoc()['total'] : 0;

/* FOTOS COM CATEGORIA */
$sql = "
SELECT 
    fotos.*,
    categorias.nome AS categoria_nome
FROM fotos
INNER JOIN categorias
ON fotos.categoria_id = categorias.id
ORDER BY fotos.id DESC
";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Painel Admin - Pixel Memories</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/admin.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

<!-- LOADING -->
<div id="loading">

    <div class="loader-box">

        <div class="spinner"></div>

        <h2>Enviando foto...</h2>

        <p>Aguarde um momento</p>

    </div>

</div>  





<!-- ALERTA -->
<div id="alerta" class="alerta">
    <i class="fa-solid fa-circle-exclamation"></i>
    <span id="alerta-mensagem"></span>
</div>

<header class="header">

    <div class="logo">
        <i class="fa-solid fa-shield-halved"></i>
        <h1>Painel Admin</h1>
    </div>

    <nav class="menu">
        <a href="index.php">Início</a>
        <a href="galeria.php">Galeria</a>
        <a href="admin.php" class="ativo">Admin</a>
        <a href="categories.php">Categorias</a>
        <a href="php/logout.php" style="color:#ff6b6b;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Sair
        </a>
    </nav>

</header>

<!-- UPLOAD -->
<section class="admin-container">

    <div class="admin-box">

        <h2><i class="fa-solid fa-upload"></i> Enviar Nova Foto</h2>

        <form id="form-upload" action="php/upload.php" method="POST" enctype="multipart/form-data">

            <label>Selecionar Imagem</label>
            <input type="file" name="foto" required>

            <label>Título</label>
            <input type="text" name="titulo" required>

            <label>Categoria</label>

            <select name="categoria" required>
                <option value="">Selecione</option>

                <?php
                $sqlCategorias = "SELECT * FROM categorias ORDER BY nome ASC";
                $resultCategorias = $conn->query($sqlCategorias);

                while($cat = $resultCategorias->fetch_assoc()) {
                ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo $cat['nome']; ?>
                    </option>
                <?php } ?>

            </select>

            <button type="submit">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Enviar Foto
            </button>

        </form>

        <div class="card-contador">
            <i class="fa-solid fa-images"></i>
            <div>
                <h3><?php echo $totalFotos; ?></h3>
                <p>Total de Fotos</p>
            </div>
        </div>

    </div>

</section>

<!-- GALERIA -->
<section class="galeria-admin">

<div class="admin-box">

    <h2><i class="fa-solid fa-images"></i> Fotos Enviadas</h2>

    <div class="grid-fotos">

        <?php while($foto = $resultado->fetch_assoc()) { ?>

        <div class="card-foto">

            <img src="<?php echo $foto['caminho_arquivo']; ?>">

            <div class="card-info">

                <h3><?php echo $foto['titulo']; ?></h3>

                <p><?php echo $foto['categoria_nome']; ?></p>

                <div class="acoes">

                    <a href="#"
                       class="btn-editar"
                       onclick="abrirModal(
                            '<?php echo $foto['id']; ?>',
                            '<?php echo addslashes($foto['titulo']); ?>',
                            '<?php echo $foto['categoria_id']; ?>'
                       )">
                        Editar
                    </a>

                 <a href="php/excluir.php?id=<?php echo $foto['id']; ?>"
                           class="btn-excluir anim-delete">

                            <i class="fa-solid fa-trash"></i> Excluir

                        </a>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

</section>

<!-- MODAL -->
<div id="modalEditar" class="modal">

    <div class="modal-content">

        <span class="fechar" onclick="fecharModal()">&times;</span>

        <h2>Editar Foto</h2>

        <form action="php/atualizar.php" method="POST">

            <input type="hidden" name="id" id="edit-id">

            <label>Título</label>
            <input type="text" name="titulo" id="edit-titulo" required>

            <label>Categoria</label>

            <select name="categoria" id="edit-categoria">

                <?php
                $sqlCategorias = "SELECT * FROM categorias ORDER BY nome ASC";
                $resultCategorias = $conn->query($sqlCategorias);

                while($cat = $resultCategorias->fetch_assoc()) {
                ?>
                    <option value="<?php echo $cat['id']; ?>">
                        <?php echo $cat['nome']; ?>
                    </option>
                <?php } ?>

            </select>

            <button type="submit">Salvar Alterações</button>

        </form>

    </div>

</div>

<!-- JS -->

<script>



/* =========================
   SISTEMA DE ALERTAS
========================= */
const urlParams = new URLSearchParams(window.location.search);
const erro = urlParams.get('erro');
const status = urlParams.get('status');
const alerta = document.getElementById('alerta');
const mensagem = document.getElementById('alerta-mensagem');

if (erro || status) {
    alerta.style.display = 'flex';
    
    // Mapeamento de mensagens
    const mensagens = {
        'muitopequeno': 'A imagem é muito pequena. Escolha um arquivo maior que 10KB.',
        'tamanho': 'Arquivo muito grande! Máximo de 20MB permitido.',
        'formato': 'Formato inválido! Use JPG, PNG ou WebP.',
        'sucesso': 'Foto enviada com sucesso!',
        'bd': 'Erro ao salvar no banco de dados.',
        'vazio': 'Selecione uma imagem antes de enviar.'
    };

    if (status === 'sucesso') {
        alerta.style.backgroundColor = '#27ae60'; // Verde para sucesso
        mensagem.innerText = mensagens['sucesso'];
    } else {
        alerta.style.backgroundColor = '#e74c3c'; // Vermelho para erro
        mensagem.innerText = mensagens[erro] || 'Ocorreu um erro inesperado.';
    }

    // Sumir após 5 segundos
    setTimeout(() => {
        alerta.style.opacity = '0';
        setTimeout(() => alerta.style.display = 'none', 500);
    }, 5000);
}


document.addEventListener('DOMContentLoaded', () => {

    /* =========================
       MODAL
    ========================= */

    window.abrirModal = function(id, titulo, categoria_id){

        document.getElementById('modalEditar').style.display = 'flex';

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-titulo').value = titulo;
        document.getElementById('edit-categoria').value = categoria_id;

    }

    window.fecharModal = function(){

        document.getElementById('modalEditar').style.display = 'none';

    }

    window.onclick = function(event){

        let modal = document.getElementById('modalEditar');

        if(event.target === modal){
            modal.style.display = 'none';
        }

    }

    /* =========================
       LOADING UPLOAD
    ========================= */

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

});



</script>

</body>
</html>