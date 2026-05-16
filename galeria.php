<?php
// 1. Conectar ao banco de dados
include "php/conexao.php";

// 2. Captura a categoria da URL (Vindo do botão Explorar da index.php)
$categoria_selecionada = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;

// 3. Monta a SQL inicial baseada na URL
$sql = "SELECT * FROM fotos";
if ($categoria_selecionada) {
    $sql .= " WHERE categoria_id = $categoria_selecionada";
}
$sql .= " ORDER BY id DESC";

$resultado_fotos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria - Pixel Memories</title>
    <link rel="icon" href="fotos/logo.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/galeria.celular.css"> 
    
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

    <!-- FILTROS -->
    <div class="filtro-categorias">
        <button class="filtro <?php echo !$categoria_selecionada ? 'ativo' : ''; ?>" data-id="0">Todas</button>

        <?php
          $sqlCat = "SELECT * FROM categorias ORDER BY nome ASC";
          $resCat = $conn->query($sqlCat);
          while($cat = $resCat->fetch_assoc()) {
            $ativo = ($categoria_selecionada == $cat['id']) ? 'ativo' : '';
            echo "<button class='filtro $ativo' data-id='{$cat['id']}'>{$cat['nome']}</button>";
        }
        ?>
    </div>

    <!-- GRID DE FOTOS -->
  <section class="galeria">
  <?php
$sql = "SELECT fotos.*, categorias.nome AS categoria_nome 
        FROM fotos 
        INNER JOIN categorias ON fotos.categoria_id = categorias.id";
$resultado_fotos = $conn->query($sql);
?>


    <?php
    if ($resultado_fotos && $resultado_fotos->num_rows > 0) {
        while($foto = $resultado_fotos->fetch_assoc()) {
    ?>
        <div class="foto">
            <img src="<?php echo htmlspecialchars($foto['caminho_arquivo']); ?>" 
                 alt="<?php echo htmlspecialchars($foto['titulo']); ?>">

            <div class="overlay">
                <h3><?php echo htmlspecialchars($foto['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($foto['categoria_nome']); ?></p>
            </div>
        </div>
    <?php  
        }
    } else {
        echo "<p style='grid-column:1/-1;text-align:center;
        padding:50px;opacity:0.5;'>Nenhuma foto encontrada.</p>";
    }
    ?>
</section>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <span class="close"><i class="fa-solid fa-xmark"></i></span>
        <img class="lightbox-img" id="lightbox-img">
    </div>

    <footer class="footer-principal">
        <p>© 2026 Pixel Memories - Todos os direitos reservados</p>
        <div class="social">
            <a href="https://www.instagram.com/lucasglimasousa12/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.facebook.com/profile.php?id=100048787846437" target="_blank"><i class="fa-brands fa-facebook"></i></a>
            <a href="#"><i class="fa-brands fa-github"></i></a>
        </div>
    </footer>

    <script>
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const close = document.querySelector('.close');

        // ABRIR LIGHTBOX
        document.addEventListener('click', (e) => {
            const fotoCard = e.target.closest('.foto');
            if (fotoCard) {
                const srcDaImagem = fotoCard.querySelector('img').src;
                lightbox.style.display = 'flex';
                lightboxImg.src = srcDaImagem;
            }
        });

        // FECHAR LIGHTBOX
        function fecharLightbox() {
            lightbox.style.display = 'none';
        }

        close.addEventListener('click', fecharLightbox);
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) fecharLightbox(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') fecharLightbox(); });

     const botoes = document.querySelectorAll('.filtro');
const galeriaGrid = document.querySelector('.galeria');

botoes.forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove ativo de todos e adiciona no clicado
        botoes.forEach(b => b.classList.remove('ativo'));
        this.classList.add('ativo');

        const id = this.dataset.id;

        // Feedback visual: opacidade baixa enquanto carrega
        galeriaGrid.style.opacity = '0.5';

        fetch("galeria_ajax.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "categoria_id=" + encodeURIComponent(id) // Uso do encodeURIComponent por segurança
        })
        .then(res => {
            if (!res.ok) throw new Error('Erro na rede');
            return res.text();
        })
        .then(data => {
            galeriaGrid.innerHTML = data;
            galeriaGrid.style.opacity = '1'; // Volta a opacidade normal
        })
        .catch(err => {
            console.error("Erro ao carregar galeria:", err);
            galeriaGrid.innerHTML = "<p>Erro ao carregar as fotos. Tente novamente.</p>";
            galeriaGrid.style.opacity = '1';
        });
    });
});

    </script>
</body>
</html>