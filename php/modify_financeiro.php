<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vencimento = $_POST['vencimento'];
    $valor = $_POST['valor'];
    $nome = $_POST['nome'];
    $pago = isset($_POST['pago']) ? '1' : '0';

    if (isset($_POST['adicionar'])) {
        $stmt = $conn->prepare("INSERT INTO financeiro (nome, vencimento, valor, pago) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $vencimento, $valor, $pago);
        if (!$stmt->execute()) {
            echo "Erro ao adicionar financeiro";
        }
        $stmt->close();
    }

    $conn->close();

    header("Location: ../contratos.php?show_modal=true");
    exit();
} else {
    header("Location: ../contratos.php");
    exit();
}

?>
