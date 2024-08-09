<?php
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require_once '../dompdf/vendor/autoload.php';
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT f.nome AS nome_financeiro, cl.nome AS nome_cliente, fo.nome AS nome_fornecedor, c.email, c.numero_local, c.email_local, c.plano, c.sla
            FROM contratos c
            LEFT JOIN financeiro f ON c.id_financeiro = f.id
            LEFT JOIN clientes cl ON c.id_cliente = cl.id
            LEFT JOIN fornecedores fo ON c.id_fornecedor = fo.id
            WHERE c.apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $html = '
        <html>
        <head>
            <style>
                body { font-family: DejaVu Sans, sans-serif; }
                table { border-collapse: collapse; width: 80%; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                td { font-size: 11px }
            </style>
        </head>
        <body>
            <h2>Relatório dos Contratos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Financeiro</th>
                        <th>Cliente</th>
                        <th>Fornecedor</th>
                        <th>E-mail</th>
                        <th>E-mail Local</th>
                        <th>Número Local</th>
                        <th>Plano</th>
                        <th>SLA</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {

            $html .= '<tr>
                        <td>'.htmlspecialchars($row['nome_financeiro']).'</td>
                        <td>'.htmlspecialchars($row['nome_cliente']).'</td>
                        <td>'.htmlspecialchars($row['nome_fornecedor']).'</td>
                        <td>'.htmlspecialchars($row['email']).'</td>
                        <td>'.htmlspecialchars($row['email_local']).'</td>
                        <td>'.htmlspecialchars($row['numero_local']).'</td>
                        <td>'.htmlspecialchars($row['plano']).'</td>
                        <td>'.htmlspecialchars($row['sla']).'</td>
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
        $dompdf->stream('contratos_relatorio_' . $data_hoje . '.pdf', ['Attachment' => 1]);
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
?>
