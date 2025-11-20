<?php
// src/actions/clientes/buscar_clientes.php

require_once __DIR__ . '/../../../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$results = [];

if (isset($_GET['termo']) && !empty($_GET['termo'])) {

    $search = '%' . $_GET['termo'] . '%';

    try {
        // Retorna cliente + veículo
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.nome,
                c.telefone,
                v.modelo AS carro,
                v.placa
            FROM clientes c
            LEFT JOIN veiculos v ON v.cliente_id = c.id
            WHERE c.nome LIKE ?
            ORDER BY c.nome
            LIMIT 15
        ");

        $stmt->execute([$search]);

        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retorna exatamente o JSON que o autocomplete do add_servico precisa
        foreach ($clientes as $c) {
            $results[] = [
                'id'       => $c['id'],
                'nome'     => $c['nome'],
                'telefone' => $c['telefone'],
                'carro'    => $c['carro'],
                'placa'    => $c['placa']
            ];
        }

    } catch (PDOException $e) {
        // Em caso de erro, retorna array vazio
        echo json_encode([]);
        exit;
    }
}

echo json_encode($results);
