<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $cnpj = str_replace(array('(', ')', '-', '.', '/'), '', $cnpj);
    $telefone_comercial = $_POST['telefone_comercial'];
    $telefone_financeiro = $_POST['telefone_financeiro'];
    $telefone_suporte = $_POST['telefone_suporte'];
    $descricao = $_POST['descricao'];

    if (isset($_POST['adicionar'])) {

        $stmt = $conn->prepare("INSERT INTO fornecedores (nome, endereco, email, cnpj, telefone_comercial, telefone_financeiro, telefone_suporte, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nome, $endereco, $email, $cnpj, $telefone_comercial, $telefone_financeiro, $telefone_suporte, $descricao);
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE fornecedores SET nome=?, endereco=?, email=?, cnpj=?, telefone_comercial=?, telefone_financeiro=?, telefone_suporte=?, descricao=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $nome, $endereco, $email, $cnpj, $telefone_comercial, $telefone_financeiro, $telefone_suporte, $descricao, $id);
    } elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE fornecedores SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);     
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    //fechar a conexao
    $stmt->close();
    $conn->close();

    header("Location: ../fornecedores.php");
    exit();
} else {
    header("Location: ../fornecedores.php");
    exit();
}
