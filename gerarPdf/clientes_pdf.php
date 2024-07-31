<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gerarRelatorio'])) {

    require('../fpdf186/fpdf.php');
    include('../db/config.php');

    $conn->set_charset('utf8mb4');

    $data_hoje = date('Y-m-d');

    $sql = "SELECT nome, endereco, email, numero_contato, descricao FROM clientes WHERE apagado=0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 12);
        
        $pdf->Cell(60, 10, 'Nome', 1, 0, 'C');
        $pdf->Cell(60, 10, mb_convert_encoding('Endereço', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(60, 10, 'E-mail', 1, 0, 'C');
        $pdf->Cell(20, 10, mb_convert_encoding('Número', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Cell(60, 10, mb_convert_encoding('Descrição', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(60, 10, mb_convert_encoding($row['nome'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(60, 10, mb_convert_encoding($row['endereco'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(60, 10, mb_convert_encoding($row['email'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(20, 10, mb_convert_encoding($row['numero_contato'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $pdf->Cell(60, 10, mb_convert_encoding($row['descricao'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            
            $pdf->Ln();
        }

        $pdf->Output('D', 'clientes_relatorio_' . $data_hoje . '.pdf');
    } else {
        echo "0 resultados";
    }

    $conn->close();
}
?>
