<?php
require_once "config.php";
include_once "templates/cabecalho.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_cadastrar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if (!empty($nome)) {
        try {
            $stmt = $conexao->prepare("INSERT INTO pacientes (nome, cpf, telefone) VALUES (:nome, :cpf, :telefone)");
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->execute();
            header("Location: pacientes.php?msg=cadastrado");
            exit();
        } catch (PDOException $e) {
            header("Location: pacientes.php?msg=erro_cadastrar");
            exit();
        }
    }
}

if (isset($_GET['id_excluir'])) {
    try {
        $stmtDel = $conexao->prepare("DELETE FROM pacientes WHERE id_paciente = :id");
        $stmtDel->bindValue(':id', $_GET['id_excluir'], PDO::PARAM_INT);
        $stmtDel->execute();
        header("Location: pacientes.php?msg=deletado");
        exit();
    } catch (PDOException $e) {
        header("Location: pacientes.php?msg=erro_excluir");
        exit();
    }
}

$stmt = $conexao->query("SELECT * FROM pacientes ORDER BY nome ASC");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'cadastrado'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">✅ Paciente cadastrado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deletado'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">✅ Paciente excluído com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_excluir'): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3">⚠️ Não foi possível excluir o paciente (ele possui consultas vinculadas).<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Listagem de Pacientes</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoPaciente">
        + Novo Paciente
    </button>
</div>

<div class="row">
    <?php if (empty($pacientes)): ?>
        <div class="alert alert-warning">Nenhum paciente cadastrado.</div>
    <?php else: ?>
        <?php foreach ($pacientes as $paciente): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">👤 <?php echo htmlspecialchars($paciente['nome']); ?></h5>
                        <p class="card-text mb-1"><strong>Telefone:</strong> <?php echo htmlspecialchars($paciente['telefone'] ?? 'Não informado'); ?></p>
                        <p class="card-text mb-3"><strong>CPF:</strong> <?php echo htmlspecialchars($paciente['cpf'] ?? 'Não informado'); ?></p>
                        
                        <hr>
                        
                        <div class="d-flex gap-2">
                            <a href="editar_paciente.php?id=<?php echo $paciente['id_paciente']; ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <a href="pacientes.php?id_excluir=<?php echo $paciente['id_paciente']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este paciente?');">❌ Excluir</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalNovoPaciente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">+ Novo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="pacientes.php">
                <div class="modal-body">
                    <input type="hidden" name="acao_cadastrar" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Ana Silva" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone / WhatsApp (Máx. 11 dígitos)</label>
                        <!-- maxlength="11" trava a digitação no 11º caractere -->
                        <input type="text" 
                               name="telefone" 
                               class="form-control" 
                               placeholder="Ex: 44999991111" 
                               maxlength="11" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CPF (Máx. 11 dígitos)</label>
                        <!-- maxlength="11" trava a digitação no 11º caractere -->
                        <input type="text" 
                               name="cpf" 
                               class="form-control" 
                               placeholder="Ex: 12345678900" 
                               maxlength="11" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "templates/footer.php"; ?>