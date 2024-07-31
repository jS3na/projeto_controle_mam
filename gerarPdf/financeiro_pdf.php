<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT nome, vencimento, valor, pago FROM financeiro WHERE apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(60, 10, 'Fatura', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Vencimento', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Valor', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Pago', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(60, 10, mb_convert_encoding($row['nome'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 10, mb_convert_encoding($row['vencimento'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 10, mb_convert_encoding($row['valor'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(20, 10, mb_convert_encoding($row['pago'] == '1' ? 'Pago' : 'Devendo', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output('D', 'financeiro_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
