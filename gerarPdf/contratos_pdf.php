<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
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

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 11);

        $pdf->Cell(35, 10, 'Financeiro', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Cliente', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Fornecedor', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Email', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Email Local', 1, 0, 'C');
        $pdf->Cell(40, 10, mb_convert_encoding('Número Local', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(25, 10, 'Plano', 1, 0, 'C');
        $pdf->Cell(40, 10, 'SLA', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(35, 25, mb_convert_encoding($row['nome_financeiro'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(35, 25, mb_convert_encoding($row['nome_cliente'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(25, 25, mb_convert_encoding($row['nome_fornecedor'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['email'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['email_local'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['numero_local'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(25, 25, mb_convert_encoding($row['plano'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(40, 25, mb_convert_encoding($row['sla'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');

            $pdf->Ln();
        }

        $pdf->Output('D', 'contratos_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
