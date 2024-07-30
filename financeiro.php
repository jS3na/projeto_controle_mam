<?php
include("./db/config.php");
$sql = "SELECT * FROM financeiro";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <nav class="sidebar">

        <ul class="list-nav">
            <li class="item-menu">
                <a href="index.html">
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
            <li class="item-menu">
                <a href="acesso.php">
                    <span class="icon"><i class="bi bi-key"></i></span>
                    <span class="txt-link">Acesso</span>
                </a>
            </li>
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
        </ul>

    </nav>

    <div class="container mt-5">
        <h1>Financeiro</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["vencimento"] . "</td>";
                        echo "<td>" . $row["valor"] . "</td>";
                        if ($row['pago'] == 0) {
                            echo "<td>" . "Não" . "</td>";
                        } else {
                            echo "<td>" . "Sim" . "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Nenhum fornecedor encontrado</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QVjwZJHLtQzjRWz5HVvXLHzEGIpuC5u6fqMzZHXAY9M8abR7E37EYtKNvEbxCZ40" crossorigin="anonymous"></script>
</body>

</html>