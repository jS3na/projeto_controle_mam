<?php

include("../db/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $cnpj = str_replace(array('(', ')', '-', '.', '/'), '', $cnpj);
    $contato_comercial = $_POST['contato_comercial'];
    $contato_financeiro = $_POST['contato_financeiro'];
    $contato_suporte = $_POST['contato_suporte'];
    $descricao = $_POST['descricao'];

    if(isset($_POST['adicionar'])){

        $stmt = $conn->prepare("INSERT INTO fornecedores (nome, endereco, email, cnpj, contato_comercial, contato_financeiro, contato_suporte, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nome, $endereco, $email, $cnpj, $contato_comercial, $contato_financeiro, $contato_suporte, $descricao);

    }
    elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE fornecedores SET nome=?, endereco=?, email=?, cnpj=?, contato_comercial=?, contato_financeiro=?, contato_suporte=?, descricao=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $nome, $endereco, $email, $cnpj, $contato_comercial, $contato_financeiro, $contato_suporte, $descricao, $id);
    }
    elseif (isset($_POST['apagar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM fornecedores WHERE id=?");
        $stmt->bind_param("i", $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    //fechar a conexao
    $stmt->close();
    $conn->close();

    header("Location: ../fornecedores.php");
    exit();

}else{
    header("Location: ../fornecedores.php");
    exit();
}

?>
