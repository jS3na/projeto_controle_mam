<?php
include '../db/config.php';

if (isset($_GET['fornecedor_id'])) {
    $fornecedor_id = $_GET['fornecedor_id'];

    $sql = "SELECT cidades.nome FROM cidades 
            JOIN fornecedores ON cidades.id_fornecedor = fornecedores.id
            WHERE fornecedores.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fornecedor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cidades = array();
    while ($row = $result->fetch_assoc()) {
        $cidades[] = $row;
    }

    echo json_encode($cidades);
}

$conn->close();
?>
