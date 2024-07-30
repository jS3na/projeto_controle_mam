<?php

include("../db/config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE users SET nome=?, email=?, senha=? WHERE id=?");
        $stmt->bind_param("sssi", $nome, $email, $senha, $id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    //fechar a conexao
    $stmt->close();
    $conn->close();

    header("Location: ../acesso.php");
    exit();

}else{
    header("Location: ../acesso.php");
    exit();
}

?>
