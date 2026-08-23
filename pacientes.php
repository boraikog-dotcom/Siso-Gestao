<?php
require_once "config.php";
include_once "cabecalho.php";

$sql = "SELECT * FROM pacientes ORDER BY nome ASC";
$executar = $conexao->query($sql);
$listaPacientes = $executar->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Listagem de Pacientes</h2>
</div>

<div class="row">
    <?php if (empty($listaPacientes)) { ?>
        <div class="alert alert-warning">Nenhum paciente encontrado.</div>
    <?php } else { ?>

        <?php foreach ($listaPacientes as $paciente) { ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">👤 <?php echo $paciente['nome']; ?></h5>
                        <p class="card-text mb-1"><strong>Telefone:</strong> <?php echo $paciente['telefone']; ?></p>
                        <p class="card-text"><strong>CPF:</strong> <?php echo $paciente['cpf']; ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

</div>
</body>

</html>