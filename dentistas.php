<?php
require_once "config.php";
include_once "cabecalho.php";

$sql = "SELECT * FROM dentistas ORDER BY nome ASC";
$executar = $conexao->query($sql);
$listaDentistas = $executar->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Corpo Clínico (Dentistas)</h2>
</div>

<div class="row">
    <?php if (empty($listaDentistas)) { ?>
        <div class="alert alert-warning">Nenhum dentista encontrado.</div>
    <?php } else { ?>

        <?php foreach ($listaDentistas as $dentista) { ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="index.php?busca=<?php echo urlencode($dentista['nome']); ?>" class="text-decoration-none text-primary">
                                🥼 <?php echo $dentista['nome']; ?>
                            </a>
                        </h5>
                        <p class="card-text mb-1"><strong>Especialidade:</strong> <?php echo $dentista['especialidades']; ?></p>
                        <p class="card-text"><strong>CRO:</strong> <?php echo $dentista['cro']; ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

</div>
</body>

</html>