<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT endereco, velocidade, sla, tipo, aprovado FROM prospeccoes WHERE apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(70, 10, mb_convert_encoding('Endereço', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(35, 10, 'Velocidade', 1, 0, 'C');
        $pdf->Cell(40, 10, 'SLA', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Tipo', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Aprovado', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(70, 10, mb_convert_encoding($row['endereco'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(35, 10, mb_convert_encoding($row['velocidade'] . ' Mbps', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 10, mb_convert_encoding($row['sla'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(35, 10, mb_convert_encoding($row['tipo'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(25, 10, mb_convert_encoding($row['aprovado'] == '1' ? 'Sim' : 'Não', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output('D', 'prospeccoes_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
