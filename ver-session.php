<?php
session_start();
echo "Valor da sessão: " . ($_SESSION['teste'] ?? "Sessão sumiu!");
?>