<?php
session_start();
include("./db/config.php");

if(empty($_SESSION['logado'])){
    header("Location: ./index.php");
    exit();
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(*) as total FROM chamados WHERE apagado = 0";
if ($search) {
    $countQuery .= " AND data_inicio LIKE ?";
}
$stmtCount = $conn->prepare($countQuery);
if ($search) {
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
}
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query = "
SELECT cl.nome AS nome_cliente, c.id, c.data_inicio, c.data_final, c.id_cliente, c.status, c.prioridade, c.tipo, c.data_previsao
FROM chamados c
LEFT JOIN clientes cl ON c.id_cliente = cl.id
WHERE c.apagado = 0
";
if ($search) {
    $query .= " AND data_inicio LIKE ?";
}
$query .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
if ($search) {
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados</title>
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
        <h1>Chamados</h1>

        <section class="topActions">
            <?php if ($_SESSION['admin']) : ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarEditar" onclick="setModalState('add')">
                    Abrir Chamado
                </button>
            <?php endif ?>

            <form class="formPesquisa" method="GET" action="chamados.php">
                <input type="text" class="form-control" id="search" name="search" placeholder="Pesquise pelo nome do Fornecedor">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </section>

        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Abrir Novo Chamado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_chamado.php">
                            <input type="hidden" name="id" id="chamadoId">
                            <label id="labelCliente">
                                <i class="bi bi-person-circle"></i>
                                <select name="id_cliente" id="clienteId" class="form-select">
                                    <option value="">Selecione o Cliente</option>
                                    <?php
                                    $clientes = $conn->query("SELECT id, nome FROM clientes");
                                    while ($cliente = $clientes->fetch_assoc()) {
                                        echo "<option value='{$cliente['id']}'>{$cliente['nome']}</option>";
                                    }
                                    ?>
                                </select>
                            </label>
                            <label>
                                <input type="checkbox" name="prioridade" id="chamadoPrioridade" value="1" />
                                <span>Prioridade</span>
                            </label>
                            <label id="labelFinalizar">
                                <input type="checkbox" name="finalizar" id="chamadoFinalizado" value="1" />
                                <span>Finalizar</span>
                            </label>
                            <label id="labelTipo">
                                <i class="bi bi-tag"></i>
                                <input name="tipo" type="text" placeholder="Tipo *" id="chamadoTipo" />
                            </label>
                            <label id="labelDataPrevisao">
                                <i class="bi bi-stopwatch"></i>
                                <span>Data de previsão</span>
                                <input name="data_previsao" type="datetime-local" placeholder="Data de previsão *" id="chamadoDataPrevisao" />
                            </label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" id="modalActionButton">Adicionar</button>
                        <button type="button" class="btn btn-primary remove" id="modalActionButton" data-bs-toggle="modal" data-bs-target="#modalApagar">
                            Apagar
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalApagar" tabindex="-1" aria-labelledby="modalApagarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalApagarLabel">Você tem certeza que deseja apagar esse Chamado?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formApagar" method="post" action="./php/modify_chamado.php">
                            <input type="hidden" name="id" id="chamadoIdApagar">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button name="apagar" type="submit" class="btn btn-danger">Apagar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        </tbody>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data de início</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Prioridade</th>
                        <th>Tipo</th>
                        <th>Data de previsão</th>
                        <th>Data de fim</th>
                        <?php if ($_SESSION['admin']) : ?>
                            <th>Ações</th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo $row['status'] == '1' ? '<tr style="background-color: #9EFBAE;">' : "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["data_inicio"] . "</td>";
                            echo "<td>" . $row["nome_cliente"] . "</td>";
                            echo $row['status'] == '1' ? '<td> Aberto </td>' : "<td> Fechado </td>";
                            echo $row['prioridade'] == '1' ? '<td style="background-color: #e0e059;"> Prioridade </td>' : "<td> Não Prioridade </td>";
                            echo "<td>" . $row["tipo"] . "</td>";
                            echo "<td>" . $row["data_previsao"] . "</td>";
                            echo "<td>" . $row["data_final"] . "</td>";
                            if($_SESSION['admin']){
                                echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Nenhum chamado encontrado</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function setModalState(action, data = {}) {
            const modalTitle = document.querySelector('#modalAdicionarEditarLabel');
            const modalActionButton = document.querySelector('#modalActionButton');
            const form = document.querySelector('#formAdicionarEditar');
            const removeButton = document.querySelector('.btn.btn-primary.remove');

            const finalizarButton = document.querySelector('#labelFinalizar');
            const tipoButton = document.querySelector('#labelTipo');
            const dataPrevisaoButton = document.querySelector('#labelDataPrevisao');
            const clienteButton = document.querySelector('#labelCliente');



            if (action === 'add') {
                modalTitle.textContent = 'Abrir Novo Chamado';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');

                removeButton.style.display = 'none';
                finalizarButton.style.display = 'none';
                tipoButton.style.display = 'block';
                dataPrevisaoButton.style.display = 'block';
                clienteButton.style.display = 'block';

                form.action = './php/modify_chamado.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar Chamado';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');

                removeButton.style.display = 'block';
                finalizarButton.style.display = 'block';
                tipoButton.style.display = 'none';
                dataPrevisaoButton.style.display = 'none';
                clienteButton.style.display = 'none';

                form.action = './php/modify_chamado.php';

                document.querySelector('#chamadoId').value = data.id;
                document.querySelector('#clienteId').value = data.id_cliente;

                document.querySelector('#chamadoPrioridade').checked = data.prioridade == '1'
                document.querySelector('#chamadoFinalizado').checked = data.status == '1'

                document.querySelector('#chamadoTipo').value = data.tipo;
                document.querySelector('#chamadoDataPrevisao').value = data.data_previsao;

                document.querySelector('#chamadoIdApagar').value = data.id;
            }
        }
    </script>
</body>

</html>