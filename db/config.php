<?php

//conexão com o banco de dados
$host = 'localhost';
$login = "root";
$senha_bd = "admin";
$banco = 'controle_mam';

$conn = new mysqli($host, $login, $senha_bd, $banco);
//verifica se a conexão foi estabelecida com sucesso
if ($conn->connect_error) {
    die('deu erro irmao ' . $conn->connect_error);
}

?>