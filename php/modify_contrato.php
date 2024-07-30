<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_financeiro = $_POST['id_financeiro'];
    $id_cliente = $_POST['id_cliente'];
    $id_fornecedor = $_POST['id_fornecedor'];
    $email = $_POST['email'];
    $numero_local = $_POST['numero_local'];
    $email_local = $_POST['email_local'];
    $plano = $_POST['plano'];
    $sla = $_POST['sla'];

    if (isset($_POST['adicionar'])) {
        $stmt = $conn->prepare("INSERT INTO contratos (id_financeiro, id_cliente, id_fornecedor, email, numero_local, email_local, plano, sla) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssss", $id_financeiro, $id_cliente, $id_fornecedor, $email, $numero_local, $email_local, $plano, $sla);
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE contratos SET id_financeiro=?, id_cliente=?, id_fornecedor=?, email=?, numero_local=?, email_local=?, plano=?, sla=? WHERE id=?");
        $stmt->bind_param("iiisssssi", $id_financeiro, $id_cliente, $id_fornecedor, $email, $numero_local, $email_local, $plano, $sla, $id);
    } elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE contratos SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "Erro ao executar a operação";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../contratos.php");
    exit();
} else {
    header("Location: ../contratos.php");
    exit();
}
