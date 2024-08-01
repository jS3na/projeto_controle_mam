<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $grupo = $_POST['grupo'];

    if (isset($_POST['adicionar'])) {

        $senha = $_POST['senha'];
        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (nome, email, senha, grupo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $grupo);
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        // Não é necessário atualizar a senha se não for fornecida
        if (!empty($_POST['senha'])) {
            $senha = $_POST['senha'];
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, senha=?, grupo=? WHERE id=?");
            $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $grupo, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, grupo=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $email, $grupo, $id);
        }
    } elseif (isset($_POST['apagar'])) {
        $apagado = 1;
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE users SET apagado=? WHERE id=?");
        $stmt->bind_param("ii", $apagado, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    header("Location: ../acesso.php");
    exit();
} else {
    header("Location: ../acesso.php");
    exit();
}
