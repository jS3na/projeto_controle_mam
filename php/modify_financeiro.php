<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['adicionar'])) {
        $vencimento = $_POST['vencimento'];
        $valor = $_POST['valor'];
        $nome = $_POST['nome'];
        $pago = isset($_POST['pago']) ? '1' : '0';
        $stmt = $conn->prepare("INSERT INTO financeiro (nome, vencimento, valor, pago) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $vencimento, $valor, $pago);
        if (!$stmt->execute()) {
            echo "Erro ao adicionar financeiro";
        }
        $stmt->close();
        $conn->close();

        header("Location: ../contratos.php?show_modal=true");
        exit();
        
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $lancado_cispro = isset($_POST['lancado_cispro']) ? '1' : '0';
        $pago = isset($_POST['pago']) ? '1' : '0';
        $stmt = $conn->prepare("UPDATE financeiro SET lancado_cispro=?, pago=? WHERE id=?");
        $stmt->bind_param("iii", $lancado_cispro, $pago, $id);
        if (!$stmt->execute()) {
            echo "Erro ao atualizar o financeiro";
        }
        $stmt->close();
        $conn->close();

        header("Location: ../financeiro.php");
        exit();
    }
} else {
    header("Location: ../contratos.php");
    exit();
}
