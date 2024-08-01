<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT nome, endereco, email, cnpj, contato_comercial, contato_financeiro, contato_suporte, descricao FROM fornecedores WHERE apagado = 0";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(50, 10, mb_convert_encoding('Endereço', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(35, 10, 'E-mail', 1, 0, 'C');
        $pdf->Cell(25, 10, 'CNPJ', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Contato comercial', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Contato financeiro', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Contato suporte', 1, 0, 'C');
        $pdf->Cell(50, 10, mb_convert_encoding('Descrição', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(50, 25, mb_convert_encoding($row['endereco'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(35, 25, mb_convert_encoding($row['email'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(25, 25, mb_convert_encoding($row['cnpj'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['contato_comercial'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['contato_financeiro'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['contato_suporte'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(50, 25, mb_convert_encoding($row['descricao'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output('D', 'fornecedores_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
