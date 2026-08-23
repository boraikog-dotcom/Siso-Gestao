<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisoGestão - Consultório Odontológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php
    $pagina_atual = basename($_SERVER['PHP_SELF']);
    ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="index.php">🦷 SisoGestão</a>
            <div class="navbar-nav">
                <a class="nav-link <?php echo ($pagina_atual == 'index.php') ? 'active' : ''; ?>" href="index.php">Consultas</a>

                <a class="nav-link <?php echo ($pagina_atual == 'pacientes.php') ? 'active' : ''; ?>" href="pacientes.php">Pacientes</a>

                <a class="nav-link <?php echo ($pagina_atual == 'dentistas.php') ? 'active' : ''; ?>" href="dentistas.php">Dentistas</a>
            </div>
        </div>
    </nav>

    <div class="container">