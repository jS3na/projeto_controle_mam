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

// Contar total de registros com ou sem filtro
$countQuery = "SELECT COUNT(*) as total FROM financeiro WHERE apagado = 0";
if ($search) {
    $countQuery .= " AND valor LIKE ?";
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

// Consulta para selecionar registros com ou sem filtro
$query = "SELECT * FROM financeiro WHERE apagado = 0";
if ($search) {
    $query .= " AND valor LIKE ?";
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
    <title>Financeiro</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Financeiro</h1>

        <section class="topActions">
            <?php if ($_SESSION['admin']) : ?>
                <!--<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarEditar" onclick="setModalState('add')">
                    Adicionar
                </button>-->
            <?php endif ?>

            <form class="formPesquisa" method="GET" action="financeiro.php">
                <input type="text" class="form-control" id="search" name="search" placeholder="Pesquise pelo valor" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </section>

        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Adicionar Novo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_financeiro.php">
                            <input type="hidden" name="id" id="financeiroId">
                            <label>
                                <input type="checkbox" name="pago" id="financeiroPago" value="1" />
                                <span>Pago</span>
                            </label>
                            <label>
                                <input type="checkbox" name="pago" id="financeiroLancadoCispro" value="1" />
                                <span>Lançado no Cispro</span>
                            </label>
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
        </div>

        <div class="modal fade" id="modalApagar" tabindex="-1" aria-labelledby="modalApagarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalApagarLabel">Você tem certeza que deseja apagar?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formApagar" method="post" action="./php/modify_financeiro.php">
                            <input type="hidden" name="id" id="financeiroIdApagar">
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
                        <th>Nome</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Pago</th>
                        <th>Lançado no CISPRO</th>
                        <?php if ($_SESSION['admin']) : ?>
                            <th>Ações</th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo $row['pago'] == '1' ? '<tr class="ok">' : "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["nome"] . "</td>";
                            echo "<td>" . $row["vencimento"] . "</td>";
                            echo "<td>" . $row["valor"] . "</td>";
                            echo $row['pago'] == '1' ? '<td> Sim </td>' : "<td> Não </td>";
                            echo $row['lancado_cispro'] == '1' ? '<td> Sim </td>' : "<td> Não </td>";
                            if ($_SESSION['admin']) {
                                echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum registro encontrado</td></tr>";
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Next">
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

            if (action === 'add') {
                modalTitle.textContent = 'Adicionar Novo';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');
                removeButton.style.display = 'none';
                form.action = './php/modify_financeiro.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');
                removeButton.style.display = 'block';
                form.action = './php/modify_financeiro.php';

                document.querySelector('#financeiroId').value = data.id;
                document.querySelector('#financeiroPago').checked = data.pago != '0'; // Ajuste conforme necessário
                document.querySelector('#financeiroLancadoCispro').checked = data.lancado_cispro != '0'; // Ajuste conforme necessário

                document.querySelector('#financeiroIdApagar').value = data.id;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>