<?php
include("./db/config.php");
$stmt = $conn->prepare("SELECT * FROM fornecedores");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <nav class="sidebar">
        <ul class="list-nav">
            <li class="item-menu">
                <a href="index.html">
                    <span class="icon"><i class="bi bi-house"></i></span>
                    <span class="txt-link">Início</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="fornecedores.php">
                    <span class="icon"><i class="bi bi-truck"></i></span>
                    <span class="txt-link">Fornecedor</span>
                </a>
            </li>
            <li class="item-menu">
                <a href="acesso.php">
                    <span class="icon"><i class="bi bi-key"></i></span>
                    <span class="txt-link">Acesso</span>
                </a>
            </li>
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
        </ul>

    </nav>

    <div class="container mt-5">
        <h1>Fornecedores</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFornecedor" onclick="setModalState('add')">
            Adicionar Fornecedor
        </button>

        <div class="modal fade" id="modalFornecedor" tabindex="-1" aria-labelledby="modalFornecedorLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFornecedorLabel">Adicionar Novo Fornecedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formFornecedor" method="post" action="./php/modify_fornecedor.php">
                            <input type="hidden" name="id" id="fornecedorId">

                            <label>
                                <i class="bi bi-person-circle"></i>
                                <input name="nome" type="text" placeholder="Nome *" id="fornecedorNome" />
                            </label>

                            <label>
                                <i class="bi bi-building"></i>
                                <input name="endereco" type="text" placeholder="Endereço *" id="fornecedorEndereco" />
                            </label>

                            <label>
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="email" placeholder="E-Mail *" id="fornecedorEmail" />
                            </label>

                            <label>
                                <i class="bi bi-postcard"></i>
                                <input name="cnpj" type="text" placeholder="CNPJ *" id="fornecedorCnpj" />
                            </label>

                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="contato_comercial" type="text" placeholder="Contato Comercial *" id="fornecedorComercial" />
                            </label>

                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="contato_financeiro" type="text" placeholder="Contato Financeiro *" id="fornecedorFinanceiro" />
                            </label>

                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="contato_suporte" type="text" placeholder="Contato Suporte *" id="fornecedorSuporte" />
                            </label>

                            <label>
                                <i class="bi bi-card-text"></i>
                                <input name="descricao" type="text" placeholder="Descrição *" id="fornecedorDescricao" />
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Email</th>
                    <th>CNPJ</th>
                    <th>Contato Comercial</th>
                    <th>Contato Financeiro</th>
                    <th>Contato Suporte</th>
                    <th>Descrição</th>
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
                        echo "<td>" . $row["endereco"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["cnpj"] . "</td>";
                        echo "<td>" . $row["contato_comercial"] . "</td>";
                        echo "<td>" . $row["contato_financeiro"] . "</td>";
                        echo "<td>" . $row["contato_suporte"] . "</td>";
                        echo "<td>" . $row["descricao"] . "</td>";
                        // Other columns
                        echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalFornecedor' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Nenhum fornecedor encontrado</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function setModalState(action, data = {}) {
            const modalTitle = document.querySelector('#modalFornecedorLabel');
            const modalActionButton = document.querySelector('#modalActionButton');
            const form = document.querySelector('#formFornecedor');

            if (action === 'add') {
                modalTitle.textContent = 'Adicionar Novo Fornecedor';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');
                form.action = './php/modify_fornecedor.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar Fornecedor';
                modalActionButton.textContent = 'Editar';
                modalActionButton.setAttribute('name', 'editar');
                form.action = './php/modify_fornecedor.php';

                document.querySelector('#fornecedorId').value = data.id;
                document.querySelector('#fornecedorNome').value = data.nome;
                document.querySelector('#fornecedorEndereco').value = data.endereco;
                document.querySelector('#fornecedorEmail').value = data.email;
                document.querySelector('#fornecedorCnpj').value = data.cnpj;
                document.querySelector('#fornecedorComercial').value = data.contato_comercial;
                document.querySelector('#fornecedorFinanceiro').value = data.contato_financeiro;
                document.querySelector('#fornecedorSuporte').value = data.contato_suporte;
                document.querySelector('#fornecedorDescricao').value = data.descricao;
            }
        }
    </script>
</body>

</html>