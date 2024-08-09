<?php
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require_once '../dompdf/vendor/autoload.php';
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT cl.nome AS nome_cliente, c.data_inicio, c.data_final, c.id_cliente, c.status, c.prioridade, c.tipo, c.data_previsao
            FROM chamados c
            LEFT JOIN clientes cl ON c.id_cliente = cl.id
            WHERE c.apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $html = '
        <html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h2>Relatório de Chamados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nome do cliente</th>
                        <th>Início</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Tipo</th>
                        <th>Previsão de término</th>
                        <th>Término</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {

            $statusText = '';

            switch ($row['status']) {
                case '0':
                    $statusText = 'Aberto';
                    break;
                case '1':
                    $statusText = 'Agendado';
                    break;
                case '2':
                    $statusText = 'Fechado';
                    break;
                default:
                    $statusText = 'Desconhecido';
                    break;
            }

            $html .= '<tr>
                        <td>'.htmlspecialchars($row['nome_cliente']).'</td>
                        <td>'.htmlspecialchars($row['data_inicio']).'</td>
                        <td>'.htmlspecialchars($statusText).'</td>
                        <td>'.htmlspecialchars($row['prioridade'] == 1 ? 'Sim' : 'Não').'</td>
                        <td>'.htmlspecialchars($row['tipo']).'</td>
                        <td>'.htmlspecialchars($row['data_previsao']).'</td>
                        <td>'.htmlspecialchars($row['data_final']).'</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('chamados_relatorio_' . $data_hoje . '.pdf', ['Attachment' => 1]);
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
?>
