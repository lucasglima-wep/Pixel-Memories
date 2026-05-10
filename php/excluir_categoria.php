<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../login.html");
    exit();
}

include "conexao.php";

if (!isset($_GET['id'])) {
    header("Location: ../categories.php?erro=id");
    exit();
}

$id = intval($_GET['id']);

/* DELETE */
$sql = "DELETE FROM categorias WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: ../categories.php?status=excluido");
    exit();

} else {

    header("Location: ../categories.php?erro=bd");
    exit();

}
?>