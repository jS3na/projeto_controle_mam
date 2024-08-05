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

<!DOCTYPE html class="theme-light">
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Chamados</h1>

        <section class="topActions">
            <?php if ($_SESSION['admin']) : ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarEditar"">
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
                            <label class="sumirNoEditar">
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
                            <label>
                                <span>Status</span>
                                <select name="status" id="chamadoStatus">
                                    <option value="0">Aberto</option>
                                    <option value="1">Agendado</option>
                                    <option value="2">Fechado</option>
                                </select>
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-tag"></i>
                                <input name="tipo" type="text" placeholder="Tipo *" id="chamadoTipo" />
                            </label>
                            <label class="sumirNoEditar">
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
                            echo $row['status'] == '1' ? '<tr class="ok">' : "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["data_inicio"] . "</td>";
                            echo "<td>" . $row["nome_cliente"] . "</td>";
                            switch ($row['status']) {
                                case '0':
                                    echo '<td> Aberto </td>';
                                    break;
                                case '1':
                                    echo '<td> Agendado </td>';
                                    break;
                                case '2':
                                    echo '<td> Fechado </td>';
                                    break;
                                default:
                                    echo '<td> Desconhecido </td>';
                                    break;
                            }
                            echo $row['prioridade'] == '1' ? '<td class="attention"> Prioridade </td>' : "<td> Não Prioridade </td>";
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

            const sumirNoEditar = document.querySelectorAll('.sumirNoEditar');

            if (action === 'add') {
                modalTitle.textContent = 'Abrir Novo Chamado';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');

                removeButton.style.display = 'none';
                sumirNoEditar.forEach(element => {
                    element.style.display = 'block';
                });

                form.action = './php/modify_chamado.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar Chamado';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');

                removeButton.style.display = 'block';
                sumirNoEditar.forEach(element => {
                    element.style.display = 'none';
                });

                form.action = './php/modify_chamado.php';

                document.querySelector('#chamadoId').value = data.id;
                document.querySelector('#clienteId').value = data.id_cliente;
                document.querySelector('#chamadoStatus').value = data.status;
                document.querySelector('#chamadoPrioridade').checked = data.prioridade == '1'

                document.querySelector('#chamadoIdApagar').value = data.id;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>