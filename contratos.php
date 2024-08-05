<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão
include("./db/config.php"); // Inclui o arquivo de configuração para conexão com o banco de dados

// Verifica se o usuário está logado; se não, redireciona para a página de login
if (empty($_SESSION['logado'])) {
    header("Location: ./index.php");
    exit();
}

// Inicializa a variável de busca
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Define a página atual e calcula o offset para a paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Consulta para contar o total de registros
$countQuery = "SELECT COUNT(*) as total FROM contratos WHERE apagado = 0";
if ($search) {
    $countQuery .= " AND email LIKE ?";
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

// Consulta para buscar os contratos com paginação e filtragem
$query = "
SELECT c.id, f.nome AS nome_financeiro, cl.nome AS nome_cliente, fo.nome AS nome_fornecedor, 
       c.email, c.numero_local, c.email_local, c.plano, c.sla
FROM contratos c
LEFT JOIN financeiro f ON c.id_financeiro = f.id
LEFT JOIN clientes cl ON c.id_cliente = cl.id
LEFT JOIN fornecedores fo ON c.id_fornecedor = fo.id
WHERE c.apagado = 0
";

if ($search) {
    $query .= " AND c.email LIKE ?";
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
    <title>Contratos</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="icon" href="https://maminfo.com.br/wp-content/uploads/2021/06/cropped-maninfo-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <?php include './sidebar.html'; ?>

    <div class="container w-20 p-3">
        <h1 class="title-page">Contratos</h1>

        <section class="topActions">
            <!-- Botão para adicionar um contrato, visível apenas para administradores -->
            <?php if ($_SESSION['admin']) : ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarFinanceiro">
                    Adicionar Contrato
                </button>
            <?php endif ?>

            <!-- Formulário de pesquisa -->
            <form class="formPesquisa" method="GET" action="contratos.php">
                <input type="text" class="form-control" id="search" name="search" placeholder="Pesquise pelo email do Contrato">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </section>

        <div class="modal fade" id="modalAdicionarFinanceiro" tabindex="-1" aria-labelledby="modalAdicionarFinanceiroLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarFinanceiroLabel">Primeiro, adicione um financeiro para vincular ao contrato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_financeiro.php">
                            <input type="hidden" name="id" id="chamadoId">
                            <label>
                                <i class="bi bi-tag"></i>
                                <input name="nome" type="text" placeholder="Nome *" id="financeiroNome" />
                            </label>
                            <label>
                                <i class="bi bi-calendar"></i>
                                <input name="vencimento" type="date" placeholder="Data de vencimento *" id="financeiroVencimento" />
                            </label>
                            <label>
                                <i class="bi bi-cash"></i>
                                <input name="valor" type="text" placeholder="Valor *" id="financeiroValor" />
                            </label>
                            <label>
                                <input type="checkbox" name="pago" id="financeiroPago" value="1" />
                                <span>Pago</span>
                            </label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" name="adicionar">Adicionar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para adicionar/editar contrato -->
        <div class="modal fade" id="modalAdicionarEditar" tabindex="-1" aria-labelledby="modalAdicionarEditarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAdicionarEditarLabel">Adicionar Novo Contrato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAdicionarEditar" method="post" action="./php/modify_contrato.php">
                            <!-- Formulário de adição/edição de contrato -->
                            <input type="hidden" name="id" id="contratoId">

                            <!-- Campos para selecionar financeiro, cliente, fornecedor e outros detalhes -->
                            <label id="labelFinanceiro">
                                <i class="bi bi-person-circle"></i>
                                <select name="id_financeiro" id="financeiroId" class="form-select">
                                    <option value="">Selecione o Financeiro</option>
                                    <?php
                                    $financeiros = $conn->query("SELECT id, nome FROM financeiro");
                                    while ($financeiro = $financeiros->fetch_assoc()) {
                                        echo "<option value='{$financeiro['id']}'>{$financeiro['nome']}</option>";
                                    }
                                    ?>
                                </select>
                            </label>

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

                            <label id="labelFornecedor">
                                <i class="bi bi-person-circle"></i>
                                <select name="id_fornecedor" id="fornecedorId" class="form-select">
                                    <option value="">Selecione o Fornecedor</option>
                                    <?php
                                    $fornecedores = $conn->query("SELECT id, nome FROM fornecedores");
                                    while ($fornecedor = $fornecedores->fetch_assoc()) {
                                        echo "<option value='{$fornecedor['id']}'>{$fornecedor['nome']}</option>";
                                    }
                                    ?>
                                </select>
                            </label>

                            <label>
                                <i class="bi bi-envelope"></i>
                                <input name="email" type="email" placeholder="Email *" id="contratoEmail" />
                            </label>
                            <label>
                                <i class="bi bi-envelope"></i>
                                <input name="email_local" type="email" placeholder="Email Local*" id="contratoEmailLocal" />
                            </label>
                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="numero_local" type="text" placeholder="Número Local *" id="contratoNumeroLocal" />
                            </label>
                            <label>
                                <i class="bi bi-telephone"></i>
                                <input name="plano" type="text" placeholder="Plano *" id="contratoPlano" />
                            </label>
                            <label style="display: flex; flex-direction: row; align-items: center;">
                                <h5 style="margin: 10px">SLA: </h5>
                                <input name="sla" type="time" placeholder="SLA *" id="contratoSla" />
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

        <!-- Modal para confirmação de exclusão -->
        <div class="modal fade" id="modalApagar" tabindex="-1" aria-labelledby="modalApagarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalApagarLabel">Você tem certeza que deseja apagar esse contrato?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formApagar" method="post" action="./php/modify_contrato.php">
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

        </tbody>

        <!-- Tabela para exibir contratos -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <!-- Cabeçalhos da tabela -->
                        <th>ID</th>
                        <th>Financeiro</th>
                        <th>Cliente</th>
                        <th>Fornecedor</th>
                        <th>Email</th>
                        <th>Número Local</th>
                        <th>Email Local</th>
                        <th>Plano</th>
                        <th>SLA</th>
                        <?php if ($_SESSION['admin']) : ?>
                            <th>Ações</th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Preenche a tabela com os dados dos contratos
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["nome_financeiro"] . "</td>";
                            echo "<td>" . $row["nome_cliente"] . "</td>";
                            echo "<td>" . $row["nome_fornecedor"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["numero_local"] . "</td>";
                            echo "<td>" . $row["email_local"] . "</td>";
                            echo "<td>" . $row["plano"] . "</td>";
                            echo "<td>" . $row["sla"] . "</td>";
                            if ($_SESSION['admin']) {
                                echo "<td><button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalAdicionarEditar' onclick='setModalState(\"edit\", " . json_encode($row) . ")'>Editar</button></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Nenhum contrato encontrado</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Navegação da página pulando de 5 em 5-->
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
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('show_modal')) {
            const modalAdicionarEditar = new bootstrap.Modal(document.getElementById('modalAdicionarEditar'));
            modalAdicionarEditar.show();
            setModalState('add');
        }
        function setModalState(action, data = {}) {
            // Seleciona o título do modal
            const modalTitle = document.querySelector('#modalAdicionarEditarLabel');
            // Seleciona o botão de ação do modal (Adicionar/Salvar Alterações)
            const modalActionButton = document.querySelector('#modalActionButton');
            // Seleciona o formulário dentro do modal
            const form = document.querySelector('#formAdicionarEditar');
            // Seleciona o botão de remover (para excluir contratos)
            const removeButton = document.querySelector('.btn.btn-primary.remove');

            const labelFinanceiro = document.querySelector('#labelFinanceiro');
            const labelFornecedor = document.querySelector('#labelFornecedor');
            const labelCliente = document.querySelector('#labelCliente');

            // Verifica se a ação é 'adicionar'
            if (action === 'add') {
                // Atualiza o título do modal para 'Adicionar Novo Contrato'
                modalTitle.textContent = 'Adicionar Novo Contrato';
                // Atualiza o texto do botão de ação para 'Adicionar'
                modalActionButton.textContent = 'Adicionar';
                // Define o nome do botão de ação como 'adicionar'
                modalActionButton.setAttribute('name', 'adicionar');
                // Oculta o botão de remover
                removeButton.style.display = 'none';

                labelFinanceiro.style.display = 'block';
                labelFornecedor.style.display = 'block';
                labelCliente.style.display = 'block';

                // Define a ação do formulário para o script PHP de modificação
                form.action = './php/modify_contrato.php';
                // Reseta o formulário para valores padrão
                form.reset();
            } else if (action === 'edit') {
                // Atualiza o título do modal para 'Editar Contrato'
                modalTitle.textContent = 'Editar Contrato';
                // Atualiza o texto do botão de ação para 'Salvar Alterações'
                modalActionButton.textContent = 'Salvar Alterações';
                // Define o nome do botão de ação como 'editar'
                modalActionButton.setAttribute('name', 'editar');
                // Exibe o botão de remover
                removeButton.style.display = 'block';

                labelFinanceiro.style.display = 'none';
                labelFornecedor.style.display = 'none';
                labelCliente.style.display = 'none';

                // Define a ação do formulário para o script PHP de modificação
                form.action = './php/modify_contrato.php';

                // Preenche os campos do formulário com os dados fornecidos
                document.querySelector('#contratoId').value = data.id;
                document.querySelector('#financeiroId').value = data.id_financeiro;
                document.querySelector('#clienteId').value = data.id_cliente;
                document.querySelector('#fornecedorId').value = data.id_fornecedor;
                document.querySelector('#contratoEmail').value = data.email;
                document.querySelector('#contratoEmailLocal').value = data.email_local;
                document.querySelector('#contratoNumeroLocal').value = data.numero_local;
                document.querySelector('#contratoPlano').value = data.plano;
                document.querySelector('#contratoSla').value = data.sla;

                // Define o valor do campo oculto para apagar com o ID do contrato
                document.querySelector('#fornecedorIdApagar').value = data.id;
            }
        }
    </script>
    <script src="./src/mudar_tema.js"></script>
</body>

</html>