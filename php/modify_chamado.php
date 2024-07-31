<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $prioridade = isset($_POST['prioridade']) ? '1' : '0';
    $tipo = $_POST['tipo'];
    $data_previsao = $_POST['data_previsao'];
    $data_previsao = str_replace('T', ' ', $data_previsao) . ':00';

    if (isset($_POST['adicionar'])) {
        $data_inicio = date('Y-m-d H:i:s');
        $status = 1;

        $stmt = $conn->prepare("INSERT INTO chamados (data_inicio, id_cliente, status, prioridade, tipo, data_previsao) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siisss", $data_inicio, $id_cliente, $status, $prioridade, $tipo, $data_previsao);
    } elseif (isset($_POST['editar'])) {
        $data_final = date('Y-m-d H:i:s');
        $status = 0;
        $id = $_POST['id'];

        $stmt = $conn->prepare("UPDATE chamados SET prioridade=?, status=?, data_final=? WHERE id=?");
        $stmt->bind_param("iisi", $prioridade, $status, $data_final, $id);
    } elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE chamados SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    // Execute a declaração preparada
    if (!$stmt->execute()) {
        // Obtenha informações sobre o erro
        $errorInfo = $stmt->error;

        // Exiba a mensagem de erro
        echo "Erro ao executar a declaração preparada: " . $errorInfo;
    }


    // Fechar a conexão
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../chamados.php");
    exit();
}
