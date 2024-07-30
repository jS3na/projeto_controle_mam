<?php

session_start();

include("./db/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logar'])) {

    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE nome = ? AND senha = ?");
    $stmt->bind_param("ss", $nome, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        //fechar a conexao
        $stmt->close();
        $conn->close();

        $_SESSION['logado'] = true;
        $_SESSION['idUser'] = $row['id'];
        $row['grupo'] === 'admin' ? $_SESSION['admin'] = true : $_SESSION['admin'] = false;

        header("Location: ./fornecedores.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <form id="formLogin" method="post" action="./index.php">
        <input type="hidden" name="id" id="fornecedorId">

        <label>
            <i class="bi bi-person-circle"></i>
            <input name="nome" type="text" placeholder="Nome *" />
        </label>

        <label>
            <i class="bi bi-key"></i>
            <input name="senha" type="password" placeholder="Senha *" />
        </label>

        <input name="logar" type="submit" value="Logar">

    </form>


</body>

</html>