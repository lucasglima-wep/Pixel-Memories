<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel Memories</title>

    <!-- Estilos e Fontes -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="fotos/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <!-- BLOCO: HEADER -->
    <header class="header">
        <div class="logo">
            <img src="fotos/logo.png" alt="Pixel Memories" class="logo-img">
            <h1>Pixel Memories</h1>
        </div>

        <nav class="menu">
            <a href="index.php" >Início</a>
            <a href="galeria.php">Galeria</a>
            <a href="sobre.html">Sobre</a>
            <a href="contato.html">Contato</a>
            <a href="login.html" class="btn-admin">
                <i class="fa-solid fa-user"></i> Admin
            </a>
        </nav>
    </header>

    <!-- BLOCO: HERO -->
    <section class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h2>Minha Galeria Pessoal</h2>
            <p>Bem-vindo ao meu espaço de memórias visuais! Aqui compartilho momentos capturados pela minha lente, 
            cada foto conta uma história única. Explore, inspire-se e reviva esses momentos comigo.</p>
            <div class="hero-buttons">
                <a href="galeria.php" class="btn">Ver Galeria</a>
                <a href="contato.html" class="btn-outline">Contato</a>
            </div>
        </div>
    </section>

    <!-- BLOCO: CATEGORIAS (PHP Dinâmico) -->
    <section class="categorias">
        <div class="titulo-section">
            <h2>Categorias</h2>
            <p>Explore minhas coleções favoritas</p>
        </div>

        <div class="cards">
            <?php
            include "php/conexao.php";

            $sql = "SELECT * FROM categorias ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($cat = $result->fetch_assoc()) {
            ?>
                    <!-- Card de Categoria -->
                    <div class="card">
                        <div class="card-img">
                            <img src="<?php echo $cat['imagem']; ?>" alt="<?php echo $cat['nome']; ?>">
                        </div>
                        <div class="card-info">
                            <h3><?php echo $cat['nome']; ?></h3>
                            <p><?php echo $cat['descricao']; ?></p>
                            <a class="btn" href="galeria.php?categoria=<?php echo $cat['id']; ?>">Explorar</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p style='color:#aaa;text-align:center;width:100%;'>Nenhuma categoria encontrada</p>";
            }
            ?>
        </div>
    </section>

    <!-- BLOCO: FOOTER -->
    <footer class="footer-principal">
        <p>© 2026 Pixel Memories - Todos os direitos reservados</p>
        <div class="social">
            <a href="https://www.instagram.com/lucasglimasousa12/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.facebook.com/profile.php?id=100048787846437" target="_blank"><i class="fa-brands fa-facebook"></i></a>
            <a href="#"><i class="fa-brands fa-github"></i></a>
        </div>
    </footer>

</body>
</html>