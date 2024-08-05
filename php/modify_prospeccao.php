<?php

include("../db/config.php");

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $stmt = $conn->prepare("SELECT nome FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['idUser']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ultima_modificacao = $row['nome'];
    }

    $endereco = $_POST['endereco'];
    $velocidade = $_POST['velocidade'];
    $sla = $_POST['sla'];
    $tipo = $_POST['tipo'];
    // Verifica se a checkbox está marcada; se não estiver, define como '0'
    $status = $_POST['status'];
    $coordenada = $_POST['latitude'] . ', ' . $_POST['longitude'];

    if(isset($_POST['adicionar'])){
        $stmt = $conn->prepare("INSERT INTO prospeccoes (endereco, coordenada, velocidade, sla, tipo, status, ultima_modificacao) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $endereco, $coordenada, $velocidade, $sla, $tipo, $status, $ultima_modificacao);
    }
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE prospeccoes SET status=?, ultima_modificacao=? WHERE id=?");
        $stmt->bind_param("isi", $status, $ultima_modificacao, $id);
    }
    elseif (isset($_POST['apagar'])) {
        $id = $_POST['id'];
        $apagado = 1;
        $stmt = $conn->prepare("UPDATE prospeccoes SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../prospeccao.php");
    exit();
}else{
    header("Location: ../prospeccao.php");
    exit();
}

?>
