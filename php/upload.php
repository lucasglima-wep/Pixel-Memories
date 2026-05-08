<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include "conexao.php";

// Verifica se o utilizador está logado
if(!isset($_SESSION['logado'])){
    header("Location: ../login.html");
    exit;
}

if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    
    $nome_ficheiro = $_FILES['foto']['name'];
    $tamanho_ficheiro = $_FILES['foto']['size'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    
    $extensao = strtolower(pathinfo($nome_ficheiro, PATHINFO_EXTENSION));
    $permitidos = array('jpg', 'jpeg', 'png', 'webp');
    
    if(!in_array($extensao, $permitidos)) {
        header("Location: ../admin.php?erro=formato");
        exit();
    }
    
    if($tamanho_ficheiro > 20 * 1024 * 1024) {
        header("Location: ../admin.php?erro=tamanho");
        exit();
    }

    $novo_nome = uniqid("img_") . ".jpg"; // Forçamos .jpg para a compressão
    $pasta_destino = "../fotos/";
    $caminho_final = $pasta_destino . $novo_nome;

    // --- SISTEMA DE COMPRESSÃO ---
    
    // 1. Criar a imagem base dependendo do formato original
    if ($extensao == 'jpg' || $extensao == 'jpeg') {
        $imagem_original = imagecreatefromjpeg($tmp_name);
    } elseif ($extensao == 'png') {
        $imagem_original = imagecreatefrompng($tmp_name);
        // Manter transparência se necessário, ou converter para fundo branco/preto
        imagepalettetotruecolor($imagem_original);
    } elseif ($extensao == 'webp') {
        $imagem_original = imagecreatefromwebp($tmp_name);
    }

    // 2. Guardar a imagem com compressão (Qualidade 60)
    // imagejpeg(recurso, destino, qualidade)
    if(imagejpeg($imagem_original, $caminho_final, 60)) {
        
        imagedestroy($imagem_original); // Liberta a memória do servidor
        
        $caminho_bd = "fotos/" . $novo_nome;
        $sql = "INSERT INTO fotos (titulo, categoria, caminho_arquivo) VALUES ('$titulo', '$categoria', '$caminho_bd')";
        
        if($conn->query($sql)) {
            header("Location: ../admin.php?status=sucesso");
            exit();
        } else {
            header("Location: ../admin.php?erro=bd");
            exit();
        }
        
    } else {
        header("Location: ../admin.php?erro=compressao");
        exit();
    }

} else {
    header("Location: ../admin.php?erro=vazio");
    exit();
}
?>