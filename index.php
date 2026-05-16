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

    
    <style>
      
        .modal {
            display: none; 
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        
        .modal-content {
            background-color: #1e1e1e; 
            color: #fff;
            margin: 10vh auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        
        .modal-content img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Botão de fechar (X) */
        .close-btn {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .close-btn:hover {
            color: #fff;
        }

       
        .modal-content h2 { margin-bottom: 10px; font-family: 'Poppins', sans-serif; }
        .modal-content p { margin-bottom: 20px; color: #ccc; font-size: 0.95rem; }
        .modal-content .btn { display: inline-block; width: 100%; box-sizing: border-box; }
    </style>
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
                    
                    <div class="card">
                        <div class="card-img">
                            <img src="<?php echo $cat['imagem']; ?>" alt="<?php echo $cat['nome']; ?>">
                        </div>
                        <div class="card-info">
                            <h3><?php echo $cat['nome']; ?></h3>
                            <p><?php echo $cat['descricao']; ?></p>
                            
                            
                            <button class="btn btn-abrir-modal" 
                                data-id="<?php echo $cat['id']; ?>"
                                data-nome="<?php echo htmlspecialchars($cat['nome'], ENT_QUOTES); ?>"
                                data-desc="<?php echo htmlspecialchars($cat['descricao'], ENT_QUOTES); ?>"
                                data-img="<?php echo htmlspecialchars($cat['imagem'], ENT_QUOTES); ?>">
                                Explorar
                            </button>
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

    
    <div id="categoriaModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <img id="modalImg" src="" alt="Imagem da Categoria">
            <h2 id="modalTitle">Título</h2>
            <p id="modalDesc">Descrição</p>
            
            <a id="modalLink" href="#" class="btn">Ver fotos da categoria</a>
        </div>
    </div>

    
    <script>
        
        const modal = document.getElementById("categoriaModal");
        const closeBtn = document.querySelector(".close-btn");
        const modalImg = document.getElementById("modalImg");
        const modalTitle = document.getElementById("modalTitle");
        const modalDesc = document.getElementById("modalDesc");
        const modalLink = document.getElementById("modalLink");

        
        const botoesAbrir = document.querySelectorAll(".btn-abrir-modal");

        
        botoesAbrir.forEach(btn => {
            btn.addEventListener("click", function() {
                
                const id = this.getAttribute("data-id");
                const nome = this.getAttribute("data-nome");
                const desc = this.getAttribute("data-desc");
                const img = this.getAttribute("data-img");

                
                modalTitle.innerText = nome;
                modalDesc.innerText = desc;
                modalImg.src = img;
                
                
                modalLink.href = "galeria.php?categoria=" + id;

                
                modal.style.display = "block";
            });
        });

        
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });

        
        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>

</body>
</html>