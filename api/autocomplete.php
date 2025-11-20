<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT nome 
        FROM clientes 
        WHERE nome LIKE ? 
        ORDER BY nome 
        LIMIT 10
    ");
    $stmt->execute(["%$term%"]);
    
    $clientes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($clientes);
    
} catch (Exception $e) {
    echo json_encode([]);
}
?>