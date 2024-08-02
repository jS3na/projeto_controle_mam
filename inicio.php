<?php
session_start();
include("./db/config.php");

if (empty($_SESSION['logado'])) {
    header("Location: ./index.php");
    exit();
}

// Atualize a consulta SQL para contar "aprovado" e "não aprovado" por tipo
$sql_prospeccoes = "SELECT tipo, aprovado, COUNT(*) as quantidade FROM prospeccoes GROUP BY tipo, aprovado";
$result_prospeccoes = $conn->query($sql_prospeccoes);

$data_prospeccoes = array();
while ($row_prospeccoes = $result_prospeccoes->fetch_assoc()) {
    $data_prospeccoes[] = $row_prospeccoes;
}
$json_data = json_encode($data_prospeccoes);

$sql_financeiro = "SELECT valor, pago, COUNT(*) as quantidade FROM financeiro GROUP BY valor, pago";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            width: 80%; /* Ajuste a largura do gráfico se necessário */
            height: 400px; /* Ajuste a altura do gráfico se necessário */
            z-index: 1;
        }
    </style>
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
        </ul>
    </nav>

    <div class="container p-3">
        <div class="chart-container">
            <canvas id="graphProspeccoes"></canvas>
        </div>
        <script>
            var ctx = document.getElementById('graphProspeccoes').getContext('2d');
            var graphProspeccoes = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [], // Tipos de prospecções
                    datasets: [
                        {
                            label: 'Aprovado',
                            data: [], // Quantidades aprovadas
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Não Aprovado',
                            data: [], // Quantidades não aprovadas
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                            }
                        }
                    }
                }
            });

            // Preencher o gráfico com os dados do PHP
            var data_prospeccoes = <?php echo $json_data; ?>;
            var labels = [];
            var approvedData = [];
            var notApprovedData = [];

            data_prospeccoes.forEach(function(item) {
                var index = labels.indexOf(item.tipo);
                if (index === -1) {
                    // Novo tipo
                    labels.push(item.tipo);
                    approvedData.push(0);
                    notApprovedData.push(0);
                    index = labels.length - 1;
                }
                if (item.aprovado == 1) {
                    approvedData[index] = item.quantidade;
                } else {
                    notApprovedData[index] = item.quantidade;
                }
            });

            graphProspeccoes.data.labels = labels;
            graphProspeccoes.data.datasets[0].data = approvedData;
            graphProspeccoes.data.datasets[1].data = notApprovedData;
            graphProspeccoes.update();
        </script>
    </div>

</body>

</html>
