<?php
require_once "config.php";
include_once "cabecalho.php";

$mensagem_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_p = $_POST['id_paciente'];
    $id_d = $_POST['id_dentista'];
    $id_pr = $_POST['id_procedimento'];
    $data = $_POST['data_consulta'];
    $hora = $_POST['hora_consulta'];

    $sql = "INSERT INTO consultas (id_paciente, id_dentista, id_procedimento, data_consulta, hora_consulta, valor_final) 
            VALUES ('$id_p', '$id_d', '$id_pr', '$data', '$hora', 0.00)";

    try {
        if ($conexao->query($sql)) {
            echo "<script>window.location.href='index.php';</script>";
            exit();
        }
    } catch (PDOException $erro) {
        $mensagem_erro = "Erro: Um ou mais IDs digitados não existem no sistema! Verifique as abas de Pacientes e Dentistas.";
    }
}
?>

<div class="card p-4 mx-auto" style="max-width: 500px;">
    <h4>📅 Agendar Consulta Curta</h4>

    <?php if ($mensagem_erro != "") { ?>
        <div class="alert alert-danger"><?php echo $mensagem_erro; ?></div>
    <?php } ?>

    <form method="POST">
        <label>ID do Paciente:</label>
        <input type="number" name="id_paciente" class="form-control mb-2" required placeholder="Ex: 1 para João">

        <label>ID do Dentista:</label>
        <input type="number" name="id_dentista" class="form-control mb-2" required placeholder="Ex: 1 para Dr. Carlos">

        <label>ID do Procedimento:</label>
        <input type="number" name="id_procedimento" class="form-control mb-2" required placeholder="Ex: 1 para Limpeza">

        <label>Data:</label>
        <input type="date" name="data_consulta" class="form-control mb-2" required>

        <label>Hora:</label>
        <input type="time" name="hora_consulta" class="form-control mb-3" required>

        <button type="submit" class="btn btn-success w-100">Confirmar Agendamento</button>
    </form>
</div>