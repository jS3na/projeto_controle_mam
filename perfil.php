<?php
session_start();
include("./db/config.php");


$query = "SELECT * FROM users WHERE id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['idUser']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html class="theme-light">
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Perfil do Usuário</h1>

        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Adicionar Novo Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_perfil.php">
                            <input type="hidden" name="id" id="perfilId">
                            <label>
                                <i class="bi bi-person-circle"></i>
                                <input name="nome" type="text" placeholder="Nome *" id="perfilNome" />
                            </label>
                            <label>
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="email" placeholder="E-Mail *" id="perfilEmail" />
                            </label>
                            <label>
                                <i class="bi bi-key"></i>
                                <input name="senha" type="password" placeholder="Nova Senha *" id="perfilSenha" />
                            </label>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" id="modalActionButton">Adicionar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Grupo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["nome"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["grupo"] . "</td>";
                            echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Nenhum usuário encontrado</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function setModalState(action, data = {}) {
            const modalTitle = document.querySelector('#modalAdicionarEditarLabel');
            const modalActionButton = document.querySelector('#modalActionButton');
            const form = document.querySelector('#formAdicionarEditar');
            
            if (action === 'edit') {
                modalTitle.textContent = 'Editar Usuário';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');
                form.action = './php/modify_perfil.php';
                console.log(data);

                document.querySelector('#perfilId').value = data.id;
                document.querySelector('#perfilNome').value = data.nome;    
                document.querySelector('#perfilEmail').value = data.email;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>