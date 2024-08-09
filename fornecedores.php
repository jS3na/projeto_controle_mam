<?php
session_start();
include("./db/config.php");

if (empty($_SESSION['logado'])) {
    header("Location: ./index.php");
    exit();
}

$search = '';
$cidade = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
if (isset($_GET['cidade'])) {
    $cidade = $_GET['cidade'];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(DISTINCT f.id) as total FROM fornecedores AS f 
               LEFT JOIN cidades ON f.id = cidades.id_fornecedor
               WHERE f.apagado = 0";
if ($search) {
    $countQuery .= " AND f.nome LIKE ?";
}
if ($cidade) {
    $countQuery .= " AND cidades.nome LIKE ?";
}

$stmtCount = $conn->prepare($countQuery);
$params = [];
if ($search) {
    $params[] = "%" . $search . "%";
}
if ($cidade) {
    $params[] = "%" . $cidade . "%";
}
if ($params) {
    
    $stmtCount->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query = "SELECT DISTINCT f.* FROM fornecedores AS f 
          LEFT JOIN cidades ON f.id = cidades.id_fornecedor
          WHERE f.apagado = 0";
if ($search) {
    $query .= " AND f.nome LIKE ?";
}
if ($cidade) {
    $query .= " AND cidades.nome LIKE ?";
}

$query .= " GROUP BY f.id LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$params = [];
if ($search) {
    $params[] = "%" . $search . "%";
}
if ($cidade) {
    $params[] = "%" . $cidade . "%";
}
$params[] = $limit;
$params[] = $offset;

$types = str_repeat("s", count($params) - 2) . "ii";
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html class="theme-light">
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Fornecedores</h1>

        <section class="topActions">
            <?php if ($_SESSION['admin']) : ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarEditar" onclick="setModalState('add')">
                    Adicionar Fornecedor
                </button>
            <?php endif ?>

            <section class="filtros">
                <form class="formPesquisa" method="GET" action="fornecedores.php">
                    <input type="text" class="form-control input-pesquisa" id="search" name="search" placeholder="Pesquise pelo nome do Fornecedor">

                    <select name="cidade" id="nomeCidadeFiltro" class="form-select">
                        <option value="">Todas as cidades</option>
                        <?php
                        $cidades_ops = $conn->query("SELECT id, nome FROM cidades");
                        while ($cidade_op = $cidades_ops->fetch_assoc()) {
                            echo "<option value='{$cidade_op['nome']}'" . (isset($_GET['cidade']) && $_GET['cidade'] == $cidade_op['nome'] ? ' selected' : '') . ">{$cidade_op['nome']}</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </form>
            </section>

        </section>

        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Adicionar Novo Fornecedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_fornecedor.php">
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
                                <input name="telefone_comercial" type="text" placeholder="Telefone Comercial *" id="fornecedorComercial" />
                            </label>
                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="telefone_financeiro" type="text" placeholder="Telefone Financeiro *" id="fornecedorFinanceiro" />
                            </label>
                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="telefone_suporte" type="text" placeholder="Telefone Suporte *" id="fornecedorSuporte" />
                            </label>
                            <label>
                                <i class="bi bi-card-text"></i>
                                <input name="descricao" type="text" placeholder="Descrição *" id="fornecedorDescricao" />
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
                        <h5 class="modal-title" id="modalApagarLabel">Você tem certeza que deseja apagar esse fornecedor?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formApagar" method="post" action="./php/modify_fornecedor.php">
                            <input type="hidden" name="id" id="fornecedorIdApagar">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button name="apagar" type="submit" class="btn btn-danger">Apagar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCidades" tabindex="-1" aria-labelledby="modalCidadesLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCidadesLabel">Cidades do(a) fornecedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body" id="modalCidadesBody">

                        <div id="cidadesList"></div>

                        <form id="formAdicionarCidade" method="post" action="./php/modify_cidade.php">
                            <input type="hidden" name="fornecedor_id" id="fornecedorIdCidade">
                            <?php if ($_SESSION['admin']) : ?>
                                <label for="cidadeNome">
                                    Nome da Cidade:
                                    <input type="text" name="nome" id="cidadeNome" required>
                                </label>
                                <button type="submit" name="adicionar" class="btn btn-primary">Adicionar Cidade</button>
                            <?php endif ?>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        </tbody>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Email</th>
                        <th>CNPJ</th>
                        <th>Telefone Comercial</th>
                        <th>Telefone Financeiro</th>
                        <th>Telefone Suporte</th>
                        <th>Descrição</th>
                        <?php if ($_SESSION['admin']) : ?>
                            <th>Ações</th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td class='nome_fornecedor' data-id='" . $row["id"] . "' data-nome='" . $row["nome"] . "' data-bs-toggle='modal' data-bs-target='#modalCidades'>" . $row["nome"] . "</td>";
                            echo "<td>" . $row["endereco"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["cnpj"] . "</td>";
                            echo "<td>" . $row["telefone_comercial"] . "</td>";
                            echo "<td>" . $row["telefone_financeiro"] . "</td>";
                            echo "<td>" . $row["telefone_suporte"] . "</td>";
                            echo "<td>" . $row["descricao"] . "</td>";
                            if ($_SESSION['admin']) {
                                echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            }
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
        document.addEventListener("DOMContentLoaded", function() {
            var nomeFornecedores = document.querySelectorAll(".nome_fornecedor");
            nomeFornecedores.forEach(function(nomeFornecedor) {
                nomeFornecedor.addEventListener("click", function() {
                    var fornecedorId = this.getAttribute("data-id");

                    var fornecedorNome = this.getAttribute("data-nome");
                    document.querySelector('#modalCidadesLabel').textContent = "Cidades do(a) " + fornecedorNome;
                    document.querySelector('#fornecedorIdCidade').value = fornecedorId;
                    fetchCidades(fornecedorId);
                });
            });
        });

        function fetchCidades(fornecedorId) {
            fetch(`./fetchs/fetch_cidades.php?fornecedor_id=${fornecedorId}`)
                .then(response => response.json())
                .then(data => {
                    var modalBody = document.getElementById("cidadesList");
                    modalBody.innerHTML = "";
                    if (data.length > 0) {
                        data.forEach(cidade => {
                            modalBody.innerHTML += `<p class="txtModal" >${cidade.nome}</p>`;
                        });
                    } else {
                        modalBody.innerHTML = '<p class="txtModal">Nenhuma cidade encontrada para este fornecedor.</p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function setModalState(action, data = {}) {
            const modalTitle = document.querySelector('#modalAdicionarEditarLabel');
            const modalActionButton = document.querySelector('#modalActionButton');
            const form = document.querySelector('#formAdicionarEditar');
            const removeButton = document.querySelector('.btn.btn-primary.remove');

            if (action === 'add') {
                modalTitle.textContent = 'Adicionar Novo Fornecedor';
                modalActionButton.textContent = 'Adicionar';
                modalActionButton.setAttribute('name', 'adicionar');
                removeButton.style.display = 'none';
                form.action = './php/modify_fornecedor.php';
                form.reset();
            } else if (action === 'edit') {
                modalTitle.textContent = 'Editar Fornecedor';
                modalActionButton.textContent = 'Salvar Alterações';
                modalActionButton.setAttribute('name', 'editar');
                removeButton.style.display = 'block';
                form.action = './php/modify_fornecedor.php';

                document.querySelector('#fornecedorId').value = data.id;
                document.querySelector('#fornecedorNome').value = data.nome;
                document.querySelector('#fornecedorEndereco').value = data.endereco;
                document.querySelector('#fornecedorEmail').value = data.email;
                document.querySelector('#fornecedorCnpj').value = data.cnpj;
                document.querySelector('#fornecedorComercial').value = data.telefone_comercial;
                document.querySelector('#fornecedorFinanceiro').value = data.telefone_financeiro;
                document.querySelector('#fornecedorSuporte').value = data.telefone_suporte;
                document.querySelector('#fornecedorDescricao').value = data.descricao;

                document.querySelector('#fornecedorIdApagar').value = data.id;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>