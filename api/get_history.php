<?php
// api/get_history.php

// Usando o mesmo caminho de include do seu 'autocomplete.php'
require '../config/db.php'; 

header('Content-Type: application/json');

if (!isset($pdo)) {
    echo json_encode(['error' => 'Falha na conexão PDO.']);
    exit;
}
if (!isset($_GET['nome']) || empty(trim($_GET['nome']))) { 
    echo json_encode(['error' => 'Nome do cliente não fornecido.']);
    exit;
}

$nome = trim($_GET['nome']); 

try {
    // 1. Encontrar o cliente
    $stmt_cliente = $pdo->prepare("SELECT id, nome, telefone FROM clientes WHERE nome = ?");
    $stmt_cliente->execute([$nome]);
    $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo json_encode(['error' => 'Cliente não encontrado.']);
        exit;
    }

    // 2. Buscar TODOS os serviços desse cliente, juntando com os veículos
    $stmt_servicos = $pdo->prepare("
        SELECT 
            s.tipo_servico, 
            s.data_servico,
            s.quilometragem,
            v.modelo,
            v.placa
        FROM 
            servicos s
        LEFT JOIN 
            veiculos v ON s.veiculo_id = v.id
        WHERE 
            s.cliente_id = ?
        ORDER BY 
            s.data_servico DESC
    ");
    $stmt_servicos->execute([$cliente['id']]);
    $servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);

    // 3. Retornar os dados combinados
    echo json_encode([
        'cliente' => $cliente,
        'servicos' => $servicos
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>