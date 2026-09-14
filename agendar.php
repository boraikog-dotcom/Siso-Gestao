<?php
require_once "config.php";
include_once "templates/cabecalho.php";

$mensagem_erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $sql = "INSERT INTO consultas (id_paciente, id_dentista, id_procedimento, data_consulta, hora_consulta, valor_final) 
                VALUES (:id_p, :id_d, :id_pr, :data_c, :hora_c, 0.00)";
        
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':id_p'   => $_POST['id_paciente'],
            ':id_d'   => $_POST['id_dentista'],
            ':id_pr'  => $_POST['id_procedimento'],
            ':data_c' => $_POST['data_consulta'],
            ':hora_c' => $_POST['hora_consulta']
        ]);

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $mensagem_erro = "Erro ao agendar consulta. Verifique se todos os campos foram selecionados.";
    }
}

$pacientes = $conexao->query("SELECT id_paciente, nome FROM pacientes ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$dentistas = $conexao->query("SELECT id_dentista, nome FROM dentistas ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card p-4 mx-auto shadow-sm" style="max-width: 500px;">
    <h4 class="mb-3">📅 Agendar Consulta</h4>

    <?php if ($mensagem_erro): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $mensagem_erro; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Paciente *</label>
            <select name="id_paciente" class="form-select" required>
                <option value="">-- Selecione o Paciente --</option>
                <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Dentista *</label>
            <select name="id_dentista" class="form-select" required>
                <option value="">-- Selecione o Dentista --</option>
                <?php foreach ($dentistas as $d): ?>
                    <option value="<?= $d['id_dentista'] ?>"><?= htmlspecialchars($d['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Procedimento *</label>
            <select name="id_procedimento" class="form-select" required>
                <option value="1">Consulta Geral / Avaliação</option>
                <option value="2">Limpeza / Profilaxia</option>
                <option value="3">Manutenção Ortodôntica</option>
                <option value="4">Restauração / Obturação</option>
                <option value="5">Extração Dentária</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Data *</label>
                <input type="date" name="data_consulta" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hora *</label>
                <input type="time" name="hora_consulta" class="form-control" required>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-secondary w-50">Cancelar</a>
            <button type="submit" class="btn btn-success w-50">Confirmar</button>
        </div>
    </form>
</div>

<?php include_once "templates/footer.php"; ?>