<?php
require_once "config.php";

try {
    $sql = "SELECT * FROM vw_dashboard_consultas";
    $executar = $conexao->query($sql);
    $listaConsultas = $executar->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($listaConsultas);
} catch (PDOException $erro) {
    echo json_encode(["erro" => "Erro ao buscar dados: " . $erro->getMessage()]);
}
?>