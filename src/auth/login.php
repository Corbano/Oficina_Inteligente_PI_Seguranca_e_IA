<?php
// A sessão já foi iniciada pelo index.php, então removemos o session_start() daqui.

// Caminhos corrigidos, pois o script agora é executado a partir da raiz (index.php)
require 'config/db.php';

// O security.php já é carregado pelo index.php, então não precisamos chamá-lo aqui de novo,
// mas as funções dele (como validate_csrf_token) já estão disponíveis.

if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    die('Falha na validação CSRF.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT id, senha FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $usuario;
        
        // Redirecionamento corrigido usando a BASE_URL
        header("Location: " . BASE_URL . "?page=dashboard");
        exit();
    } else {
        // Redirecionamento corrigido usando a BASE_URL
        header("Location: " . BASE_URL . "?page=login&error=1");
        exit();
    }
}
?>