<?php
session_start();
include("./db/config.php");

// Variável para armazenar mensagens de erro
$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logar'])) {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    // Preparar a consulta para obter o hash da senha do banco de dados
    $stmt = $conn->prepare("SELECT id, senha, grupo FROM users WHERE nome = ?");
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $senhaHash, $grupo);
        $stmt->fetch();

        // Verificar a senha fornecida com o hash armazenado
        if (password_verify($senha, $senhaHash)) {
            // Senha está correta
            $_SESSION['logado'] = true;
            $_SESSION['idUser'] = $id;
            $_SESSION['admin'] = ($grupo === 'admin');

            // Fechar a conexão
            $stmt->close();
            $conn->close();

            header("Location: ./fornecedores.php");
            exit();
        } else {
            // Senha incorreta
            $loginError = 'Usuário ou senha inválidos!';
        }
    } else {
        // Usuário não encontrado
        $loginError = 'Usuário ou senha inválidos!';
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-login">
        <div class="login-form-container">
            <form id="formLogin" method="post" action="./index.php">
                <h1 class="login-title">Login</h1>
                <?php if ($loginError): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="nome">
                        <i class="bi bi-person-circle"></i>
                        <input id="nome" name="nome" type="text" placeholder="Nome *" class="form-control" required />
                    </label>
                </div>
                <div class="form-group">
                    <label for="senha">
                        <i class="bi bi-key"></i>
                        <input id="senha" name="senha" type="password" placeholder="Senha *" class="form-control" required />
                    </label>
                </div>
                <button name="logar" type="submit" class="btn btn-primary">Logar</button>
            </form>
        </div>
    </div>
</body>
</html>
