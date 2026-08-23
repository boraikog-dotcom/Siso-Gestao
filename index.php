<?php
require_once "config.php";
include_once "cabecalho.php";

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Consulta via View criada para a Sprint 1
$sql = "SELECT * FROM vw_dashboard_consultas";

if ($busca != '') {
    $sql = $sql . " WHERE paciente_nome LIKE '%$busca%' OR dentista_nome LIKE '%$busca%'";
}

$executar = $conexao->query($sql);
$listaConsultas = $executar->fetchAll(PDO::FETCH_ASSOC);

$totalConsultas = count($listaConsultas);

if (isset($_GET['id_excluir'])) {
    $id_para_excluir = $_GET['id_excluir'];
    $sqlDeletar = "DELETE FROM consultas WHERE id_consulta = '$id_para_excluir'";

    if ($conexao->query($sqlDeletar)) {
        echo "<script>window.location.href='index.php';</script>";
        exit();
    }
}
?>

<!-- Formulario de Busca Mantido -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex">
            <input class="form-control me-2" type="text" name="busca" placeholder="Buscar paciente pelo nome..." value="<?php echo htmlspecialchars($busca); ?>">
            <button class="btn btn-primary me-2" type="submit">Pesquisar</button>
            <a href="index.php" class="btn btn-secondary">Limpar</a>
        </form>
    </div>
</div>

<!-- Alert do Contador Mantido -->
<div class="alert alert-info mb-4">
    Atualmente existem <?php echo $totalConsultas; ?> consultas no sistema.
</div>

<!-- Indicadores de Totais gerados via JS/TS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Faturamento Total</h5>
                <h3 id="card-faturamento" class="card-text">R$ 0,00</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total de Consultas</h5>
                <h3 id="card-total" class="card-text"><?php echo $totalConsultas; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Procedimentos >= R$ 150</h5>
                <h3 id="card-filtrados" class="card-text">0</h3>
            </div>
        </div>
    </div>
</div>

<a href="agendar.php" class="btn btn-success mb-3">+ Agendar Nova Consulta</a>
<h2>Agenda de Consultas</h2>

<!-- Cards com Layout Original Mantido -->
<div class="row">
    <?php if (empty($listaConsultas)) { ?>
        <div class="alert alert-warning">Nenhuma consulta encontrada.</div>
    <?php } else { ?>

        <?php foreach ($listaConsultas as $consulta) { ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"> 👤 Paciente: <?php echo $consulta['paciente_nome']; ?></h5>
                        <p class="card-text mb-1"><strong>Dentista:</strong> <?php echo $consulta['dentista_nome']; ?></p>
                        <p class="card-text mb-1"><strong>Procedimento:</strong> <?php echo $consulta['nome_procedimento']; ?></p>
                        <p class="card-text mb-3"><strong>Data:</strong> <?php echo $consulta['data_consulta']; ?> às <?php echo $consulta['hora_consulta']; ?></p>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">Valor Original: R$ <?php echo $consulta['valor_base']; ?></small>
                                <br>
                                <strong>Valor com Desconto: R$ <span class="preco-final"><?php echo $consulta['valor_final']; ?></span></strong>
                            </div>

                            <a href="index.php?id_excluir=<?php echo $consulta['id_consulta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
                                ❌ Excluir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<!-- Script TS/JS da Sprint 1 -->
<script src="app.js"></script>

</div>
</body>
</html>