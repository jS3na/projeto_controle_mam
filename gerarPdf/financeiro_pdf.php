<?php
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require_once '../dompdf/vendor/autoload.php';
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT nome, vencimento, valor, pago, lancado_cispro FROM financeiro WHERE apagado = 0";

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
            <h2>Relatório dos Financeiros</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Pago</th>
                        <th>Lançado no Cispro</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {

            $html .= '<tr>
                        <td>'.htmlspecialchars($row['nome']).'</td>
                        <td>'.htmlspecialchars($row['vencimento']).'</td>
                        <td>'.htmlspecialchars($row['valor']).'</td>
                        <td>'.htmlspecialchars($row['pago'] == 1 ? 'Sim' : 'Não').'</td>
                        <td>'.htmlspecialchars($row['lancado_cispro'] == 1 ? 'Sim' : 'Não').'</td>
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
        $dompdf->stream('financeiros_relatorio_' . $data_hoje . '.pdf', ['Attachment' => 1]);
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
?>
