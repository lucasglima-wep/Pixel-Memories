<?php
session_start();
$_SESSION['teste'] = "Docker Funciona!";
echo "Sessão iniciada. <a href='ver-session.php'>Clique aqui</a>";
?>

