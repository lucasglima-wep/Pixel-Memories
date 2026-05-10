<?php

 var_dump($foto); 
 
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
    <link rel="stylesheet" href="css/galeri,celular.css">
    <link rel="icon" href="fotos/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <header class="header">
    <div class="logo">
        <img src="fotos/logo.png" alt="Pixel Memories" class="logo-img">
        <h1>Pixel Memories</h1>
    </div>
        <nav class="menu">
            <a href="index.php">Início</a>
            <a href="galeria.php" class="ativo">Galeria</a>
            <a href="sobre.html">Sobre</a>
            <a href="contato.html">Contato</a>
        </nav>
    </header>

    <section class="topo-galeria">
        <h2>Galeria de Fotos</h2>
        <p>Explore momentos incríveis registrados pela câmera.</p>
    </section>

<div class="filtro-categorias">

    <button class="filtro ativo" data-id="0">Todas</button>

    <?php
    $sqlCat = "SELECT * FROM categorias ORDER BY nome ASC";
    $resCat = $conn->query($sqlCat);

    while($cat = $resCat->fetch_assoc()) {
    ?>

        <button class="filtro" data-id="<?php echo $cat['id']; ?>">
            <?php echo $cat['nome']; ?>
        </button>

    <?php } ?>

</div>

<section class="galeria">

<?php
if ($result && $result->num_rows > 0) {

    while($foto = $result->fetch_assoc()) {
?>

<div class="foto" data-categoria="<?php echo $foto['categoria_id']; ?>">

    <img src="<?php echo $foto['caminho_arquivo']; ?>" 
            alt="<?php echo htmlspecialchars($foto['titulo']); ?>">

    <div class="overlay">

        <h3><?php echo $foto['titulo']; ?></h3>

        

        <button>
            <i class="fa-solid fa-expand"></i>
        </button>

    </div>

</div>

<?php
    }

} else {
    echo "<p style='grid-column:1/-1;text-align:center;padding:50px;opacity:0.5;'>Nenhuma foto encontrada na galeria.</p>";
}
?>

</section>

</section>



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
            <i class="fa-brands fa-github"></i>
        </a>
    </div>
</footer>

   <script>
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const close = document.querySelector('.close');

    // 1. ABRIR LIGHTBOX (Usando delegação de evento para funcionar com o AJAX)
    document.addEventListener('click', (e) => {
        // Verifica se clicou na div .foto ou em qualquer elemento dentro dela (img, botão, etc)
        const fotoCard = e.target.closest('.foto');
        
        if (fotoCard) {
            const srcDaImagem = fotoCard.querySelector('img').src;
            lightbox.style.display = 'flex';
            lightboxImg.src = srcDaImagem;

            // Tenta colocar em TELA CHEIA (Fullscreen API)
            if (lightbox.requestFullscreen) {
                lightbox.requestFullscreen();
            } else if (lightbox.webkitRequestFullscreen) { // Safari
                lightbox.webkitRequestFullscreen();
            } else if (lightbox.msRequestFullscreen) { // IE11
                lightbox.msRequestFullscreen();
            }
        }
    });

    // 2. FECHAR LIGHTBOX
    function fecharLightbox() {
        lightbox.style.display = 'none';
        // Sai do modo tela cheia se o navegador estiver nele
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
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

    // 3. FILTROS VIA AJAX
    const botoes = document.querySelectorAll('.filtro');
    const galeria = document.querySelector('.galeria');

    botoes.forEach(btn => {
        btn.addEventListener('click', function() {
            // Estilo ativo nos botões
            botoes.forEach(b => b.classList.remove('ativo'));
            this.classList.add('ativo');

            const id = this.dataset.id;

            fetch("galeria_ajax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "categoria_id=" + id
            })
            .then(res => res.text())
            .then(data => {
                galeria.innerHTML = data;
                // Nota: O clique nas fotos continuará funcionando aqui 
                // porque usamos o document.addEventListener lá em cima.
            });
        });
    });
</script>

    </script>



</body>
</html>