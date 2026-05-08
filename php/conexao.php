<?php

$host = "localhost";
$usuario = "root";
$senha = "usbw";
$banco = "pixel_memories";

$conn = new mysqli($host,$usuario,$senha,$banco);

if($conn->connect_error){
    die("Erro na conexão");
}

?>  