<?php
require_once "config.php";
include_once "templates/cabecalho.php";

$msg = "";

// 1. LÓGICA DE CADASTRO DE NOVO DENTISTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_cadastrar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cro = trim($_POST['cro'] ?? '');
    
    // Pega a especialidade selecionada no <select> ou a digitada caso escolha "Outra"
    $especialidades = trim($_POST['especialidades_select'] ?? '');
    if ($especialidades === 'Outra' && !empty($_POST['especialidades_outra'])) {
        $especialidades = trim($_POST['especialidades_outra']);
    }

    if (!empty($nome)) {
        try {
            $stmt = $conexao->prepare("INSERT INTO dentistas (nome, cro, especialidades) VALUES (:nome, :cro, :esp)");
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cro', $cro);
            $stmt->bindValue(':esp', $especialidades);
            $stmt->execute();
            header("Location: dentistas.php?msg=cadastrado");
            exit();
        } catch (PDOException $e) {
            $msg = "erro_cadastrar";
        }
    }
}

// 2. LÓGICA DE EXCLUSÃO DE DENTISTA
if (isset($_GET['id_excluir'])) {
    try {
        $stmtDel = $conexao->prepare("DELETE FROM dentistas WHERE id_dentista = :id");
        $stmtDel->bindValue(':id', $_GET['id_excluir'], PDO::PARAM_INT);
        $stmtDel->execute();
        header("Location: dentistas.php?msg=deletado");
        exit();
    } catch (PDOException $e) {
        header("Location: dentistas.php?msg=erro_excluir");
        exit();
    }
}

// 3. CONSULTA OS DENTISTAS
$stmt = $conexao->query("SELECT * FROM dentistas ORDER BY nome ASC");
$dentistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- MENSAGENS DE NOTIFICAÇÃO -->
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'cadastrado'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">✅ Dentista cadastrado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deletado'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">✅ Dentista excluído com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- CABEÇALHO COM BOTÃO QUE ABRE O MODAL -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Corpo Clínico (Dentistas)</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoDentista">
        + Novo Dentista
    </button>
</div>

<!-- LISTA DE DENTISTAS -->
<div class="row">
    <?php if (empty($dentistas)): ?>
        <div class="alert alert-warning">Nenhum dentista cadastrado.</div>
    <?php else: ?>
        <?php foreach ($dentistas as $dentista): ?>
            <?php $esp = !empty($dentista['especialidades']) ? $dentista['especialidades'] : 'Não informada'; ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">🥼 <?php echo htmlspecialchars($dentista['nome']); ?></h5>
                        <p class="card-text mb-1"><strong>Especialidade:</strong> <?php echo htmlspecialchars($esp); ?></p>
                        <p class="card-text mb-3"><strong>CRO:</strong> <?php echo htmlspecialchars($dentista['cro'] ?? 'Não informado'); ?></p>
                        <hr>
                        <div class="d-flex gap-2">
                            <a href="dentistas.php?id_excluir=<?php echo $dentista['id_dentista']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este dentista?');">❌ Excluir</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- JANELA MODAL PARA CADASTRO RÁPIDO COM OPÇÕES DE ESPECIALIDADE -->
<div class="modal fade" id="modalNovoDentista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">+ Novo Dentista</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="dentistas.php">
                <div class="modal-body">
                    <input type="hidden" name="acao_cadastrar" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Dr. João Santos" required>
                    </div>

                    <!-- SELEÇÃO DE ESPECIALIDADES DA ODONTOLOGIA -->
                    <div class="mb-3">
                        <label class="form-label">Especialidade *</label>
                        <select name="especialidades_select" id="especialidades_select" class="form-select" onchange="verificarOutraEspecialidade(this)">
                            <option value="">-- Selecione uma opção --</option>
                            <option value="Clínica Geral">Clínica Geral</option>
                            <option value="Ortodontia">Ortodontia</option>
                            <option value="Endodontia (Canal)">Endodontia (Canal)</option>
                            <option value="Outra">Outra (digitar)...</option>
                        </select>
                    </div>

                    <!-- CAMPO QUE APARECE SE ESCOLHER "OUTRA" -->
                    <div class="mb-3 d-none" id="div_outra_especialidade">
                        <label class="form-label">Digite a Especialidade</label>
                        <input type="text" name="especialidades_outra" class="form-control" placeholder="Ex: Radiologia Odontológica">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">CRO</label>
                        <input type="text" name="cro" class="form-control" placeholder="Ex: CRO-PR 67890">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "templates/footer.php"; ?>