<?php
// Força o PHP a mostrar qualquer erro que aconteça
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Ambiente - Pixel Memories</h1>";

// 1. Testar Extensão GD
echo "<h3>1. Verificando Biblioteca de Imagem (GD):</h3>";
if (function_exists('imagecreatefromjpeg')) {
    echo "<p style='color: green;'>✅ GD está ATIVA!</p>";
} else {
    echo "<p style='color: red;'>❌ GD está DESATIVADA. Verifique o seu php.ini.</p>";
}

// 2. Testar Conexão com o Banco
echo "<h3>2. Verificando Conexão com o Banco:</h3>";
$caminho_conexao = 'php/conexao.php';

if (file_exists($caminho_conexao)) {
    include($caminho_conexao);
    if (isset($conn) && !$conn->connect_error) {
        echo "<p style='color: green;'>✅ Conexão OK!</p>";
    } else {
        echo "<p style='color: red;'>❌ Arquivo de conexão carregado, mas a conexão falhou.</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ O arquivo '$caminho_conexao' não foi encontrado. Verifique se a pasta e o nome estão certos.</p>";
}

echo "<hr><p>Se você não vê nem as mensagens coloridas acima, o problema está no servidor Apache do USBWebserver.</p>";
?>