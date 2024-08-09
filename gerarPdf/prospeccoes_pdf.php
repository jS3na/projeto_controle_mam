<?php
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require_once '../dompdf/vendor/autoload.php';
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT id_solicitacao, endereco, coordenada, velocidade, sla, tipo, status, ultima_modificacao FROM prospeccoes WHERE apagado = 0";

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
            <h2>Relatório das Prospecções</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Solicitação</th>
                        <th>Endereço</th>
                        <th>Coordenada</th>
                        <th>Velocidade</th>
                        <th>SLA</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Última modificação feita por</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {

            $statusText = '';

            switch ($row['status']) {
                case '0':
                    $statusText = 'Novo';
                    break;
                case '1':
                    $statusText = 'Em Análise';
                    break;
                case '2':
                    $statusText = 'Aguardando Aprovação';
                    break;
                case '3':
                    $statusText = 'Aprovado';
                    break;
                case '4':
                    $statusText = 'Em Negociação';
                    break;
                case '5':
                    $statusText = 'Contratado';
                    break;
                default:
                    $statusText = 'Desconhecido';
                    break;
            }
            $html .= '<tr>
                        <td>'.htmlspecialchars($row['id_solicitacao']).'</td>
                        <td>'.htmlspecialchars($row['endereco']).'</td>
                        <td>'.htmlspecialchars($row['coordenada']).'</td>
                        <td>'.htmlspecialchars($row['velocidade']).'</td>
                        <td>'.htmlspecialchars($row['sla']).'</td>
                        <td>'.htmlspecialchars($row['tipo']).'</td>
                        <td>'.htmlspecialchars($statusText).'</td>
                        <td>'.htmlspecialchars($row['ultima_modificacao']).'</td>
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
        $dompdf->stream('prospeccoes_relatorio_' . $data_hoje . '.pdf', ['Attachment' => 1]);
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
?>
