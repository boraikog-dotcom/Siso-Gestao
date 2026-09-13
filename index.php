<?php
require_once "config.php";
include_once "templates/cabecalho.php";

if (isset($_GET['id_excluir'])) {
    $id_excluir = $_GET['id_excluir'];

    try {
        $stmtDel = $conexao->prepare("DELETE FROM consultas WHERE id_consulta = :id");
        $stmtDel->bindValue(':id', $id_excluir, PDO::PARAM_INT);

        if ($stmtDel->execute()) {
            header("Location: index.php?msg=deletado");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?msg=erro");
        exit();
    }
}

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10;

$stmt = $conexao->prepare("CALL sp_listar_consultas(:busca, :limite, :pagina)");
$stmt->bindValue(':busca', $busca, PDO::PARAM_STR);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':pagina', $pagina, PDO::PARAM_INT);
$stmt->execute();

$listaConsultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$sqlContador = "SELECT COUNT(*) as total FROM consultas";
$executarContador = $conexao->query($sqlContador);
$totalConsultas = $executarContador->fetch(PDO::FETCH_ASSOC)['total'];

$sqlFaturamento = "SELECT SUM(valor_base) as total_faturamento FROM consultas JOIN procedimentos ON consultas.id_procedimento = procedimentos.id_procedimento";
$execFaturamento = $conexao->query($sqlFaturamento);
$rowFaturamento = $execFaturamento->fetch(PDO::FETCH_ASSOC);
$faturamentoTotal = $rowFaturamento['total_faturamento'] ?? 0;

$sqlProc150 = "SELECT COUNT(*) as total FROM consultas JOIN procedimentos ON consultas.id_procedimento = procedimentos.id_procedimento WHERE procedimentos.valor_base >= 150";
$execProc150 = $conexao->query($sqlProc150);
$totalProc150 = $execProc150->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deletado'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        ✅ <strong>Sucesso!</strong> Consulta excluída com sucesso.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro'): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        ⚠️ <strong>Erro:</strong> Não foi possível excluir a consulta.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex">
            <input class="form-control me-2" type="text" name="busca" placeholder="Buscar paciente pelo nome..." value="<?php echo htmlspecialchars($busca); ?>">
            <button class="btn btn-primary me-2" type="submit">Pesquisar</button>
            <a href="index.php" class="btn btn-secondary">Limpar</a>
        </form>
    </div>
</div>

<div class="alert alert-info mb-4" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
    Atualmente existem <?php echo $totalConsultas; ?> consultas no sistema.
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-white h-100 shadow-sm" style="background-color: #198754;">
            <div class="card-body">
                <h5 class="card-title">Faturamento Total</h5>
                <h2 class="card-text">R$ <?php echo number_format($faturamentoTotal, 2, ',', '.'); ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card text-white h-100 shadow-sm" style="background-color: #0d6efd;">
            <div class="card-body">
                <h5 class="card-title">Total de Consultas</h5>
                <h2 class="card-text"><?php echo $totalConsultas; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white h-100 shadow-sm" style="background-color: #0dcaf0;">
            <div class="card-body">
                <h5 class="card-title">Procedimentos >= R$ 150</h5>
                <h2 class="card-text"><?php echo $totalProc150; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <a href="agendar.php" class="btn btn-success">+ Agendar Nova Consulta</a>
</div>

<h2>Agenda de Consultas</h2>

<div class="row">
    <?php if (empty($listaConsultas)) { ?>
        <div class="alert alert-warning">Nenhuma consulta encontrada.</div>
    <?php } else { ?>

        <?php foreach ($listaConsultas as $consulta) { ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">👤 Paciente: <?php echo htmlspecialchars($consulta['paciente_nome']); ?></h5>
                        <p class="card-text mb-1"><strong>Dentista:</strong> <?php echo htmlspecialchars($consulta['dentista_nome']); ?></p>
                        <p class="card-text mb-1"><strong>Procedimento:</strong> <?php echo htmlspecialchars($consulta['nome_procedimento']); ?></p>
                        <p class="card-text mb-3"><strong>Data:</strong> <?php echo htmlspecialchars($consulta['data_consulta']); ?> às <?php echo htmlspecialchars($consulta['hora_consulta']); ?></p>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">Valor Original: R$ <?php echo number_format($consulta['valor_base'], 2, ',', '.'); ?></small>
                                <br>
                                <strong>Valor com Desconto: R$ <span class="preco-final" data-valor="<?php echo $consulta['valor_base']; ?>"><?php echo number_format($consulta['valor_base'], 2, ',', '.'); ?></span></strong>
                            </div>

                            <div>
                                <a href="editar_consulta.php?id=<?php echo $consulta['id_consulta']; ?>" class="btn btn-warning btn-sm me-1">✏️ Editar</a>
                                <a href="index.php?id_excluir=<?php echo $consulta['id_consulta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">❌ Excluir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<?php include_once "templates/footer.php"; ?>