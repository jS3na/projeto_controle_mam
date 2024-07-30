<?php

include("../db/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $vencimento = $_POST['vencimento'];
    $valor = $_POST['valor'];
    // Verifica se a checkbox está marcada; se não estiver, define como '0'
    $pago = isset($_POST['pago']) ? '1' : '0';

    if(isset($_POST['adicionar'])){
        $stmt = $conn->prepare("INSERT INTO financeiro (vencimento, valor, pago) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $vencimento, $valor, $pago);
    }
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE financeiro SET vencimento=?, valor=?, pago=? WHERE id=?");
        $stmt->bind_param("sssi", $vencimento, $valor, $pago, $id);
    }
    elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE financeiro SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    header("Location: ../financeiro.php");
    exit();
}else{
    header("Location: ../financeiro.php");
    exit();
}

?>
