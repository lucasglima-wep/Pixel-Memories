<?php
// 1. Conectar ao banco de dados
include "php/conexao.php";

// 2. Buscar as fotos no banco (ordenadas pelas mais recentes)
$sql = "SELECT * FROM fotos ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria - Pixel Memories</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/galeria,celular.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <header class="header">
       <div class="logo">
        <img src="fotos/logo do site.png" alt="Pixel Memories" class="logo-img">
        <h1>Pixel Memories</h1>
    </div>
        <nav class="menu">
            <a href="index.html">Início</a>
            <a href="galeria.php" class="ativo">Galeria</a>
            <a href="sobre.html">Sobre</a>
            <a href="contato.html">Contato</a>
        </nav>
    </header>

    <section class="topo-galeria">
        <h2>Galeria de Fotos</h2>
        <p>Explore momentos incríveis registrados pela câmera.</p>
    </section>

    <section class="galeria">

        <?php
        // 3. Verificar se existem fotos no banco
        if ($result && $result->num_rows > 0) {
            // 4. Rodar o loop para cada foto encontrada
            while($foto = $result->fetch_assoc()) {
                ?>
                <div class="foto">
                    <img src="<?php echo $foto['caminho_arquivo']; ?>" alt="<?php echo $foto['categoria']; ?>">

                    <div class="overlay">
                        <h3><?php echo $foto['titulo']; ?></h3>
                        <p><?php echo $foto['categoria']; ?></p>
                        <button>
                            <i class="fa-solid fa-expand"></i>
                        </button>
                    </div>
                </div>
                <?php
            }
        } else {
            // Mensagem caso não tenha nenhuma foto ainda
            echo "<p style='grid-column: 1/-1; text-align: center; padding: 50px; opacity: 0.5;'>Nenhuma foto encontrada na galeria.</p>";
        }
        ?>

    </section>

    <div class="lightbox" id="lightbox">
        <span class="close">
            <i class="fa-solid fa-xmark"></i>
        </span>
        <img class="lightbox-img" id="lightbox-img">
    </div>

   <footer>
    <p>© 2026 Pixel Memories - Todos os direitos reservados</p>
    
    <div class="social">
        <a href="https://www.instagram.com/lucasglimasousa12/" target="_blank" rel="noopener noreferrer">
            <i class="fa-brands fa-instagram"></i>
        </a>

        <a href="https://www.facebook.com/profile.php?id=100048787846437" target="_blank" rel="noopener noreferrer">
            <i class="fa-brands fa-facebook"></i>
        </a>

        <a href="https://github.com/lucasglima-wep" target="_blank" rel="noopener noreferrer">
            <i class="fa-brands fa-github"></i>
        </a>
    </div>
</footer>

    <script>
        const fotos = document.querySelectorAll('.foto');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const close = document.querySelector('.close');

        fotos.forEach(foto => {
            foto.addEventListener('click', () => {
                const srcDaImagem = foto.querySelector('img').src;
                lightbox.style.display = 'flex';
                lightboxImg.src = srcDaImagem;
            });
        });

        function fecharLightbox() {
            lightbox.style.display = 'none';
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        }

        close.addEventListener('click', fecharLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                fecharLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                fecharLightbox();
            }
        });
    </script>

</body>
</html>