<?php

include("../db/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $email = $_POST['email'];
    $numero_contato = $_POST['numero_contato'];
    $descricao = $_POST['descricao'];

    if(isset($_POST['adicionar'])){

        $stmt = $conn->prepare("INSERT INTO clientes (nome, endereco, email, numero_contato, descricao) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $endereco, $email, $numero_contato, $descricao);

    }
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE clientes SET nome=?, endereco=?, email=?, numero_contato=?, descricao=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $endereco, $email, $numero_contato, $descricao, $id);
    }
    elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE clientes SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    //fechar a conexao
    $stmt->close();
    $conn->close();

    header("Location: ../clientes.php");
    exit();

}else{
    header("Location: ../clientes.php");
    exit();
}

?>
