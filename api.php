<?php
require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT * FROM vw_dashboard_consultas";
    $executar = $conexao->query($sql);
    $listaConsultas = $executar->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($listaConsultas);
} catch (PDOException $erro) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao buscar dados: " . $erro->getMessage()]);
}
?>