<?php
session_start();
include("./db/config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

if (empty($_SESSION['logado'])) {
    header("Location: ./index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reportar'])) {

    $bug = $_POST['bug'];

    $query = "SELECT nome FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['idUser']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc()['nome'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'netgts14@gmail.com';
        $mail->Password = 'yxcb wpfz fseh johi';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('netgts14@gmail.com');
        $mail->addAddress('joaogabriel.sena@gtsnet.com.br');

        $mail->isHTML(true);
        $mail->Subject = 'Bug Report de ' . $user;
        $mail->Body = $bug;

        if ($mail->send()) {
            echo '<script>alert("Bug reportado com sucesso!")</script>';
            header('Location: ./reportar_bug.php');
            exit();
        } else {
            echo '<script>alert("Erro ao reportar bug. Tente novamente.")</script>';
        }
    } catch (Exception $e) {
        echo '<script>alert("Erro ao enviar o e-mail: ' . $mail->ErrorInfo . '")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Bug</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <nav class="sidebar">
        <ul class="list-nav">
            <li class="item-menu">
                <a href="fornecedores.php">
                    <span class="icon"><i class="bi bi-truck"></i></span>
                    <span class="txt-link">Fornecedor</span>
                </a>
            </li>
            <?php if ($_SESSION['admin']) : ?>
                <li class="item-menu">
                    <a href="acesso.php">
                        <span class="icon"><i class="bi bi-key"></i></span>
                        <span class="txt-link">Acesso</span>
                    </a>
                </li>
            <?php endif ?>
            <li class="item-menu">
                <a href="clientes.php">
                    <span class="icon"><i class="bi bi-people-fill"></i></span>
                    <span class="txt-link">Clientes</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="contratos.php">
                    <span class="icon"><i class="bi bi-file-earmark-text"></i></span>
                    <span class="txt-link">Contratos</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="financeiro.php">
                    <span class="icon"><i class="bi bi-currency-dollar"></i></span>
                    <span class="txt-link">Financeiro</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="prospeccao.php">
                    <span class="icon"><i class="bi bi-search"></i></span>
                    <span class="txt-link">Prospecção</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="chamados.php">
                    <span class="icon"><i class="bi bi-exclamation-circle"></i></span>
                    <span class="txt-link">Chamados</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="relatorios.php">
                    <span class="icon"><i class="bi bi-graph-up"></i></span>
                    <span class="txt-link">Relatórios</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="perfil.php">
                    <span class="icon"><i class="bi bi-person"></i></span>
                    <span class="txt-link">Perfil</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="reportar_bug.php">
                    <span class="icon"><i class="bi bi-bug"></i></span>
                    <span class="txt-link">Reportar Bug</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="logout.php">
                    <span class="icon"><i class="bi bi-box-arrow-left" style="color:red"></i></span>
                    <span class="txt-link" style="color:red">Sair</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="container w-20 p-3">
        <h1>Reportar Bug</h1>

        <form action="./reportar_bug.php" method="post">
            <div class="mb-3">
                <label for="bug" class="form-label">Descrição do Bug:</label>
                <textarea class="form-control" id="bug" name="bug" rows="3" required></textarea>
            </div>
            <button type="submit" name="reportar" class="btn btn-primary">Reportar</button>
            <a href="chamados.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
