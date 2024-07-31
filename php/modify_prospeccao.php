<?php

include("../db/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $endereco = $_POST['endereco'];
    $velocidade = $_POST['velocidade'];
    $sla = $_POST['sla'];
    $tipo = $_POST['tipo'];
    // Verifica se a checkbox está marcada; se não estiver, define como '0'
    $aprovado = isset($_POST['aprovado']) ? '1' : '0';

    if(isset($_POST['adicionar'])){
        $stmt = $conn->prepare("INSERT INTO prospeccoes (endereco, velocidade, sla, tipo, aprovado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $endereco, $velocidade, $sla, $tipo, $aprovado);
    }
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE prospeccoes SET endereco=?, velocidade=?, sla=?, tipo=?, aprovado=? WHERE id=?");
        $stmt->bind_param("sssssi", $endereco, $velocidade, $sla, $tipo, $aprovado, $id);
    }
    elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE prospeccoes SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    header("Location: ../prospeccao.php");
    exit();
}else{
    header("Location: ../prospeccao.php");
    exit();
}

?>
