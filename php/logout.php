<?php
// 1. Inicia a sessão
session_start();

// 2. Limpa os dados da sessão
$_SESSION = array();

// 3. Destrói o cookie de sessão no navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destrói a sessão no servidor
session_destroy();

// 5. Redireciona para o login enviando o parâmetro de status
// O JavaScript vai ler este 'status=sucesso' para mostrar o alerta
header("location: ../login.html?status=sucesso");
exit;
?>