<?php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "sisogestao";

try {
    $conexao = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erro) {
    echo "Erro na conexao: " . $erro->getMessage();
    exit();
}