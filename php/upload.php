<?php
// 1. Configurações de Erro e Sessão
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 2. Conexão e Segurança de Acesso
include "conexao.php"; 

if (!isset($_SESSION['logado'])) {
    header("Location: ../login.html");
    exit();
}

// 3. Verifica se o formulário foi enviado corretamente
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $titulo    = trim($_POST['titulo']);
    $categoria = intval($_POST['categoria']);
    $tamanho   = $_FILES['foto']['size'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $nome_orig = $_FILES['foto']['name'];

    // --- VALIDAÇÕES DE TAMANHO ---
    $tamanho_minimo = 10 * 1024; // 10 KB
    $tamanho_maximo = 20 * 1024 * 1024; // 20 MB

    if ($tamanho < $tamanho_minimo) {
        header("Location: ../admin.php?erro=muitopequeno");
        exit();
    }

    if ($tamanho > $tamanho_maximo) {
        header("Location: ../admin.php?erro=tamanho");
        exit();
    }

    // --- VALIDAÇÃO DE EXTENSÃO ---
    $ext = strtolower(pathinfo($nome_orig, PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        header("Location: ../admin.php?erro=formato");
        exit();
    }

    // --- PREPARAÇÃO DO ARQUIVO ---
    $novo_nome = uniqid("img_") . ".jpg";
    $pasta = "../fotos/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $caminho_final = $pasta . $novo_nome;

    // --- PROCESSAMENTO DA IMAGEM ---
    $imagem_original = null;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $imagem_original = @imagecreatefromjpeg($tmp_name);
            break;
        case 'png':
            $imagem_original = @imagecreatefrompng($tmp_name);
            if ($imagem_original) imagepalettetotruecolor($imagem_original);
            break;
        case 'webp':
            $imagem_original = @imagecreatefromwebp($tmp_name);
            break;
    }

    if (!$imagem_original) {
        header("Location: ../admin.php?erro=imagem");
        exit();
    }

    // Aplica entrelaçamento (progressivo) e salva como JPG comprimido
    imageinterlace($imagem_original, true);
    
    if (imagejpeg($imagem_original, $caminho_final, 60)) {
        imagedestroy($imagem_original);

        $caminho_bd = "fotos/" . $novo_nome;

        // --- BANCO DE DADOS (PREPARED STATEMENT) ---
        $sql = "INSERT INTO fotos (titulo, categoria_id, caminho_arquivo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sis", $titulo, $categoria, $caminho_bd);
            
            if ($stmt->execute()) {
                header("Location: ../admin.php?status=sucesso");
            } else {
                header("Location: ../admin.php?erro=bd");
            }
            $stmt->close();
        } else {
            header("Location: ../admin.php?erro=sql_prepare");
        }
        exit();

    } else {
        header("Location: ../admin.php?erro=compressao");
        exit();
    }
    
    } else {
        header("Location: ../admin.php?status=edi");
        exit();
    }

//  else {
//     // Se caiu aqui, ou o arquivo é muito grande para o servidor ou nada foi enviado
//     header("Location: ../admin.php?erro=vazio");
//     exit();
// }
?>