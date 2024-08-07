<?php

include("../db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['adicionar'])) {

        $fornecedor_id = $_POST['fornecedor_id'];
        $nome = $_POST['nome'];

        $stmt = $conn->prepare("INSERT INTO cidades (nome, id_fornecedor) VALUES (?, ?)");
        $stmt->bind_param("si", $nome, $fornecedor_id);
    }

    if (!$stmt->execute()) {
        echo "deu erro";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    header("Location: ../fornecedores.php");
    exit();
} else {
    header("Location: ../fornecedores.php");
    exit();
}
