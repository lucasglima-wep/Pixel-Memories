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

/* CONEXÃO COM BANCO */
include "php/conexao.php";

/* CONTADOR DE FOTOS */
$sqlCount = "SELECT COUNT(*) AS total FROM fotos";
$resultCount = $conn->query($sqlCount);

$totalFotos = 0;

if ($resultCount) {
    $row = $resultCount->fetch_assoc();
    $totalFotos = $row['total'];
}
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

<!-- ALERTA (SÓ UM) -->
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

        <a href="index.html">Início</a>
        <a href="galeria.php">Galeria</a>
        <a href="admin.php" class="ativo">Admin</a>

        <a href="../index.html" style="color:#ff6b6b;">
            <i class="fa-solid fa-right-from-bracket"></i>
            Sair
        </a>

    </nav>

</header>

<section class="admin-container">

    <div class="admin-box">

        <h2>
            <i class="fa-solid fa-upload"></i>
            Enviar Nova Foto
        </h2>

        <p>Adicione imagens para sua galeria pessoal.</p>

        <form action="php/upload.php" method="POST" enctype="multipart/form-data">

            <label>Selecionar Imagem</label>
            <input type="file" name="foto" required>

            <label>Título da Foto</label>
            <input type="text" name="titulo" required>

            <label>Categoria</label>
            <select name="categoria">
                <option value="Natureza">Natureza</option>
                <option value="Cidade">Cidade</option>
                <option value="Noite">Noite</option>
                <option value="Animais">Animais</option>
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

<section class="galeria-admin">

    <div class="admin-box">

        <h2>
            <i class="fa-solid fa-images"></i>
            Fotos Enviadas
        </h2>

        <div class="grid-fotos">

<?php

include "php/conexao.php";

$sql = "SELECT * FROM fotos ORDER BY id DESC";
$resultado = $conn->query($sql);

while($foto = $resultado->fetch_assoc()) {

?>

            <div class="card-foto">

                <img src="<?php echo $foto['caminho_arquivo']; ?>" alt="<?php echo $foto['categoria']; ?>">

                <div class="card-info">

                    <h3><?php echo $foto['titulo']; ?></h3>
                    <p><?php echo $foto['categoria']; ?></p>

                    <div class="acoes">

                        <a href="#" class="btn-editar"
   onclick="abrirModal(
        '<?php echo $foto['id']; ?>',
        '<?php echo addslashes($foto['titulo']); ?>',
        '<?php echo $foto['categoria']; ?>'
   )">

    <i class="fa-solid fa-pen"></i> Editar

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


<!-- MODAL EDITAR -->
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

                <option value="Natureza">Natureza</option>
                <option value="Cidade">Cidade</option>
                <option value="Noite">Noite</option>
                <option value="Animais">Animais</option>

            </select>

            <button type="submit">Salvar Alterações</button>

        </form>

    </div>

</section>

<script>

/* ALERTA */
const urlParams = new URLSearchParams(window.location.search);

const status = urlParams.get('status');
const erro = urlParams.get('erro');

const alerta = document.getElementById('alerta');
const mensagem = document.getElementById('alerta-mensagem');

function mostrarAlerta(tipo, texto, icone){

    alerta.classList.remove('erro', 'sucesso');
    alerta.classList.add('show', tipo);

    alerta.querySelector('i').className = icone;
    mensagem.innerText = texto;

    setTimeout(() => {
        alerta.classList.remove('show');
    }, 3000);
}

/* ERROS */
if (erro) {

    if (erro === 'formato')
        mostrarAlerta('erro','Formato inválido!','fa-solid fa-circle-xmark');

    else if (erro === 'tamanho')
        mostrarAlerta('erro','Arquivo muito grande!','fa-solid fa-circle-xmark');

    else if (erro === 'upload')
        mostrarAlerta('erro','Erro ao enviar imagem!','fa-solid fa-circle-xmark');

    else if (erro === 'bd')
        mostrarAlerta('erro','Erro no banco!','fa-solid fa-circle-xmark');

    else if (erro === 'vazio')
        mostrarAlerta('erro','Nenhuma imagem!','fa-solid fa-circle-xmark');

}

/* SUCESSO */
if (status === 'sucesso') {
    mostrarAlerta('sucesso','Foto enviada com sucesso!','fa-solid fa-circle-check');
}

/* DELETE ANIMADO */
document.querySelectorAll('.anim-delete').forEach(btn => {

    btn.addEventListener('click', function(e) {

        const ok = confirm('Deseja excluir esta foto?');

        if (!ok) {
            e.preventDefault();
            return;
        }

        const card = this.closest('.card-foto');

        if (card) {

            card.style.transition = "0.4s";
            card.style.transform = "scale(0.8)";
            card.style.opacity = "0";

            setTimeout(() => {
                window.location.href = this.href;
            }, 300);

            e.preventDefault();

        }

    });

});


function abrirModal(id, titulo, categoria){

    document.getElementById('modalEditar').style.display = 'flex';

    document.getElementById('edit-id').value = id;
    document.getElementById('edit-titulo').value = titulo;
    document.getElementById('edit-categoria').value = categoria;

}

function fecharModal(){
    document.getElementById('modalEditar').style.display = 'none';
}

window.onclick = function(event){
    let modal = document.getElementById('modalEditar');
    if(event.target === modal){
        modal.style.display = 'none';
    }
}



</script>

</body>
</html>