<?php
session_start();
if(!isset($_SESSION['logado'])){
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Pixel Memories</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="logo">
        <i class="fa-solid fa-shield-halved"></i>
        <h1>Painel Admin</h1>
    </div>
    <nav class="menu">
        <a href="index.html">Início</a>
        <a href="galeria.php">Galeria</a>
        <a href="admin.php" class="ativo">Admin</a>
        <a href="../index.html" style="color: #ff6b6b;"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
    </nav>
</header>

<section class="admin-container">
    <div class="admin-box">
        
      <div id="alerta" class="alerta-escondido" style="margin-bottom: 20px;">
    <i class="fa-solid fa-circle-exclamation"></i>
    <span id="alerta-mensagem"></span>
</div>

        <h2><i class="fa-solid fa-upload"></i> Enviar Nova Foto</h2>
        <p>Adicione imagens para sua galeria pessoal</p>

        <form action="php/upload.php" method="POST" enctype="multipart/form-data">
            <label>Selecionar Imagem</label>
            <input type="file" name="foto" required>

            <label>Título da Foto</label>
            <input type="text" name="titulo" placeholder="Ex: Pôr do sol na praia" required>

            <label>Categoria</label>
            <select name="categoria">
                <option value="Natureza">Natureza</option>
                <option value="Cidade">Cidade</option>
                <option value="Noite">Noite</option>
                <option value="Animais">Animais</option>
            </select>

            <button type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> Enviar Foto</button>
        </form>
    </div>
</section>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const erro = urlParams.get('erro');
    const alerta = document.getElementById('alerta');
    const mensagem = document.getElementById('alerta-mensagem');

    if (erro) {
        alerta.classList.remove('alerta-escondido');
        alerta.classList.add('alerta-exibido', 'erro');

        if (erro === 'formato') mensagem.innerText = "Apenas arquivos JPG, PNG, GIF e WEBP são aceitos.";
        else if (erro === 'tamanho') mensagem.innerText = "A imagem é muito grande (Máx: 20MB).";
        else if (erro === 'upload') mensagem.innerText = "Erro ao mover o arquivo para a pasta.";
        else if (erro === 'bd') mensagem.innerText = "Erro ao salvar no banco de dados.";
        else if (erro === 'vazio') mensagem.innerText = "Nenhuma imagem foi selecionada.";
    }

    if (status === 'sucesso') {
        alerta.classList.remove('alerta-escondido');
        alerta.classList.add('alerta-exibido', 'sucesso');
        alerta.querySelector('i').className = "fa-solid fa-circle-check";
        mensagem.innerText = "Foto enviada com sucesso!";
    }

    if (erro || status) {
        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
            alerta.classList.remove('alerta-exibido', 'erro', 'sucesso');
            alerta.classList.add('alerta-escondido');
        }, 4000); // Esconde após 4 segundos
    }
</script>

</body>
</html>