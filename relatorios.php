<?php
session_start();
include("./db/config.php");

if (empty($_SESSION['logado'])) {
    header("Location: ./index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <nav class="sidebar">
        <ul class="list-nav">
            <li class="item-menu">
                <a href="inicio.php">
                    <span class="icon"><i class="bi bi-house"></i></span>
                    <span class="txt-link">Início</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="fornecedores.php">
                    <span class="icon"><i class="bi bi-truck"></i></span>
                    <span class="txt-link">Fornecedor</span>
                </a>
            </li>
            <?php if ($_SESSION['admin']) : ?>
                <li class="item-menu">
                    <a href="acesso.php">
                        <span class="icon"><i class="bi bi-key"></i></span>
                        <span class="txt-link">Acesso</span>
                    </a>
                </li>
            <?php endif ?>
            <li class="item-menu">
                <a href="clientes.php">
                    <span class="icon"><i class="bi bi-people-fill"></i></span>
                    <span class="txt-link">Clientes</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="contratos.php">
                    <span class="icon"><i class="bi bi-file-earmark-text"></i></span>
                    <span class="txt-link">Contratos</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="financeiro.php">
                    <span class="icon"><i class="bi bi-currency-dollar"></i></span>
                    <span class="txt-link">Financeiro</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="prospeccao.php">
                    <span class="icon"><i class="bi bi-search"></i></span>
                    <span class="txt-link">Prospecção</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="chamados.php">
                    <span class="icon"><i class="bi bi-exclamation-circle"></i></span>
                    <span class="txt-link">Chamados</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="relatorios.php">
                    <span class="icon"><i class="bi bi-graph-up"></i></span>
                    <span class="txt-link">Relatórios</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="perfil.php">
                    <span class="icon"><i class="bi bi-person"></i></span>
                    <span class="txt-link">Perfil</span>
                </a>
            </li>
        </ul>

    </nav>

    <div class="container w-20 p-3">
        <h1>Gerar Relatório</h1>
        <form action="./gerarPdf/<?php echo $tableName . "_pdf"; ?>.php" method="post">
            <label>
                <select name="relatorio" id="selectRelatorioId" class="form-select">
                    <option value="">Selecione o Relatório a ser feito:</option>
                    <?php
                    $tables = $conn->query("SHOW TABLES");
                    while ($row = $tables->fetch_assoc()) {
                        $tableName = $row[array_keys($row)[0]]; // Obtenha o primeiro (e único) valor no array associativo
                        // Exclui as tabelas 'users' e 'tbteste'
                        if ($tableName !== 'users' && $tableName !== 'tbteste') {
                            $formatadoNome = ucfirst($tableName); // Coloca a primeira letra maiúscula
                            echo "<option value='{$tableName}'>{$formatadoNome}</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <label>
                <button name="gerarRelatorio" type="submit" class="btn btn-primary">Gerar</button>
            </label>
        </form>

    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            var selectedValue = document.querySelector('#selectRelatorioId').value;
            if (selectedValue) {
                this.action = './gerarPdf/' + selectedValue + '_pdf.php';
            } else {
                e.preventDefault(); // Previna o envio do formulário se nada for selecionado
                alert('Por favor, selecione um relatório.');
            }
        });
    </script>

</body>

</html>