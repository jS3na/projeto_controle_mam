<?php
session_start();
include("./db/config.php");

if (empty($_SESSION['logado'])) {
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

$countQuery = "SELECT COUNT(*) as total FROM prospeccoes WHERE apagado = 0";
if ($search) {
    $countQuery .= " AND nome LIKE ?";
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

$query = "SELECT * FROM prospeccoes WHERE apagado = 0";
if ($search) {
    $query .= " AND nome LIKE ?";
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
    <title>Prospeccoes</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Prospeccoes</h1>

        <section class="topActions">
            <?php if ($_SESSION['admin']) : ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarEditar" onclick="setModalState('add')">
                    Adicionar Prospeccao
                </button>
            <?php endif ?>

            <form class="formPesquisa" method="GET" action="prospeccoes.php">
                <input type="text" class="form-control" id="search" name="search" placeholder="Pesquise pelo tipo da Prospeccao">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </section>

        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Adicionar Nova Prospeccao</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_prospeccao.php">
                            <input type="hidden" name="id" id="prospeccaoId">
                            <label class="sumirNoEditar">
                                <i class="bi bi-building"></i>
                                <input name="endereco" type="text" placeholder="Endereço *" id="prospeccaoEndereco" />
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-compass"></i>
                                <input name="latitude" type="text" placeholder="Latitude *" id="prospeccaoLatitude" />
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-compass"></i>
                                <input name="longitude" type="text" placeholder="Longitude *" id="prospeccaoLongitude" />
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-speed"></i>
                                <input name="velocidade" type="text" placeholder="Velocidade em Mbps*" id="prospeccaoVelocidade" />
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-stopwatch"></i>
                                <input name="sla" type="time" placeholder="SLA *" id="prospeccaoSla" />
                            </label>
                            <label class="sumirNoEditar">
                                <i class="bi bi-cable"></i>
                                <input name="tipo" type="text" placeholder="Tipo *" id="prospeccaoTipo" />
                            </label>
                            <label>
                                <span>Status</span>
                                <select name="status" id="prospeccaoStatus">
                                    <option value="0">Novo</option>
                                    <option value="1">Em Análise</option>
                                    <option value="2">Aguardando Aprovação</option>
                                    <option value="3">Aprovado</option>
                                    <option value="4">Em Negociação</option>
                                    <option value="5">Contratado</option>
                                </select>
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
                        <h5 class="modal-title" id="modalApagarLabel">Você tem certeza que deseja apagar essa prospeccao?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formApagar" method="post" action="./php/modify_prospeccao.php">
                            <input type="hidden" name="id" id="prospeccaoIdApagar">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button name="apagar" type="submit" class="btn btn-danger">Apagar</button>
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
                        <th>Endereço</th>
                        <th>Coordenada</th>
                        <th>Velocidade</th>
                        <th>SLA</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Última modificação</th>
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
                            echo "<td>" . $row["endereco"] . "</td>";
                            echo "<td>" . $row["coordenada"] . "</td>";
                            echo "<td>" . $row["velocidade"] . " Mbps</td>";
                            echo "<td>" . $row["sla"] . "</td>";
                            echo "<td>" . $row["tipo"] . "</td>";
                            switch ($row['status']) {
                                case '0':
                                    echo '<td> Novo </td>';
                                    break;
                                case '1':
                                    echo '<td> Em Análise </td>';
                                    break;
                                case '2':
                                    echo '<td> Aguardando Aprovação </td>';
                                    break;
                                case '3':
                                    echo '<td> Aprovado </td>';
                                    break;
                                case '4':
                                    echo '<td> Em Negociação </td>';
                                    break;
                                case '5':
                                    echo '<td> Contratado </td>';
                                    break;
                                default:
                                    echo '<td> Desconhecido </td>';
                                    break;
                            }
                            echo "<td>" . $row["ultima_modificacao"] . "</td>";

                            if($_SESSION['admin']){
                                echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Nenhuma prospecção encontrada</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Navegação de página exemplo">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= $search ?>" tabindex="-1">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= $search ?>">Próximo</a>
                </li>
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

            document.querySelector('#prospeccaoId').value = data.id;

            if (action === 'add') {
                modalTitle.textContent = 'Adicionar Nova Prospeccao';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');

                removeButton.style.display = 'none';
                sumirNoEditar.forEach(element => {
                    element.style.display = 'block';
                });

                form.action = './php/modify_prospeccao.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar Prospeccao';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');

                removeButton.style.display = 'block';
                sumirNoEditar.forEach(element => {
                    element.style.display = 'none';
                });

                form.action = './php/modify_prospeccao.php';
                document.querySelector('#prospeccaoStatus').value = data.status;

                document.querySelector('#prospeccaoIdApagar').value = data.id;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>