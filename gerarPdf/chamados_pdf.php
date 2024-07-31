<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT cl.nome AS nome_cliente, c.data_inicio, c.data_final, c.id_cliente, c.status, c.prioridade, c.tipo, c.data_previsao
            FROM chamados c
            LEFT JOIN clientes cl ON c.id_cliente = cl.id
            WHERE c.apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(40, 10, mb_convert_encoding('Início', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(60, 10, 'Cliente', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Status', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Prioridade', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Tipo', 1, 0, 'C');
        $pdf->Cell(40, 10, mb_convert_encoding('Previsão', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(40, 10, mb_convert_encoding('Término', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(40, 10, mb_convert_encoding($row['data_inicio'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(60, 10, mb_convert_encoding($row['nome_cliente'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(20, 10, mb_convert_encoding($row['status'] == '1' ? 'Aberto' : 'Fechado', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(25, 10, mb_convert_encoding($row['prioridade'] == '1' ? 'Prioridade' : 'Não Prioridade', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(60, 10, mb_convert_encoding($row['tipo'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 10, mb_convert_encoding($row['data_previsao'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 10, mb_convert_encoding($row['data_final'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output('D', 'chamados_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
