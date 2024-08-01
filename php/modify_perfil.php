<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        
        if (!empty($senha)) {
            // Criptografar a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, senha=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $email, $senhaHash, $id);
        } else {
            // Atualizar sem mudar a senha
            $stmt = $conn->prepare("UPDATE users SET nome=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $nome, $email, $id);
        }

        if (!$stmt->execute()) {
            echo "Erro ao atualizar o usuário!";
        }
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    header("Location: ../perfil.php");
    exit();

} else {
    header("Location: ../perfil.php");
    exit();
}

?>
