<?php
// Exibir erros apenas para debug (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 1. Conexão
include "conexao.php"; 

// 2. Segurança - Resposta JSON em vez de redirecionamento
if (!isset($_SESSION['logado'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado']);
    exit();
}

// 3. Verifica se o formulário foi enviado
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $titulo    = trim($_POST['titulo']);
    $categoria = intval($_POST['categoria']);
    $tamanho   = $_FILES['foto']['size'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $nome_orig = $_FILES['foto']['name'];

    // --- VALIDAÇÕES ---
    $tamanho_minimo = 10 * 1024; 
    $tamanho_maximo = 20 * 1024 * 1024; 

    if ($tamanho < $tamanho_minimo) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'muitopequeno']);
        exit();
    }

    if ($tamanho > $tamanho_maximo) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'tamanho']);
        exit();
    }

    $ext = strtolower(pathinfo($nome_orig, PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'formato']);
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
        echo json_encode(['status' => 'erro', 'mensagem' => 'imagem']);
        exit();
    }

    imageinterlace($imagem_original, true);
    
    if (imagejpeg($imagem_original, $caminho_final, 60)) {
        imagedestroy($imagem_original);

        $caminho_bd = "fotos/" . $novo_nome;

        // --- BANCO DE DADOS ---
        $sql = "INSERT INTO fotos (titulo, categoria_id, caminho_arquivo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sis", $titulo, $categoria, $caminho_bd);
            
            if ($stmt->execute()) {
                // SUCESSO REAL
                echo json_encode(['status' => 'sucesso']);
            } else {
                echo json_encode(['status' => 'erro', 'mensagem' => 'bd']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'erro', 'mensagem' => 'sql_prepare']);
        }
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'compressao']);
    }
    exit();

} else {
    // Caso nenhum arquivo tenha sido enviado ou erro de upload
    echo json_encode(['status' => 'erro', 'mensagem' => 'vazio']);
    exit();
}