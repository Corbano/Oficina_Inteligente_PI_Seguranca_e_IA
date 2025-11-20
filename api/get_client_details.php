<?php
// api/get_client_details.php
// Este script busca OS DADOS de um cliente para o autocomplete do formulário.

// Usando o mesmo caminho de include do seu 'autocomplete.php'
require '../config/db.php'; 

header('Content-Type: application/json');

if (!isset($pdo)) {
    echo json_encode(['error' => 'PDO connection failed']);
    exit;
}
if (!isset($_GET['nome']) || empty(trim($_GET['nome']))) { 
    echo json_encode(['error' => 'Nome do cliente não fornecido']);
    exit;
}

$nome = trim($_GET['nome']); 

try {
    // CORREÇÃO: Busca (JOIN) em 'clientes' e 'veiculos'
    // E lê as colunas 'modelo' e 'placa'
    $stmt = $pdo->prepare("
        SELECT 
            c.nome, 
            c.telefone,
            v.modelo AS carro,  -- Renomeia 'modelo' para 'carro' (para o JS)
            v.placa
        FROM clientes c
        LEFT JOIN veiculos v ON c.id = v.cliente_id
        WHERE c.nome = ?
        ORDER BY v.id DESC -- Pega o veículo mais recente deste cliente
        LIMIT 1
    ");
    $stmt->execute([$nome]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        // Retorna o JSON que o JS espera (cli.nome, cli.telefone, cli.carro, cli.placa)
        echo json_encode($cliente, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => 'Cliente não encontrado']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>