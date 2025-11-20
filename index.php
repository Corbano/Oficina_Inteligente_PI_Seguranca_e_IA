<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// index.php - Controlador principal (simplificado)

// Carrega segurança e DB sempre (necessário para templates e actions)
require_once __DIR__ . '/src/core/security.php';
require_once __DIR__ . '/config/db.php';

// =====================
// AÇÕES (endpoints tipo action=...)
// =====================
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Garantir que db e security estão carregados
    require_once __DIR__ . '/config/db.php';
    require_once __DIR__ . '/src/core/security.php';

    switch ($action) {
        case 'do_login':
            require __DIR__ . '/src/auth/login.php';
            break;

        case 'do_logout':
            $_SESSION = [];
            session_destroy();
            header('Location: index.php?page=login');
            exit;
            break;

        case 'save_cliente':
            require __DIR__ . '/src/actions/save_cliente.php';
            break;

        case 'save_servico':
            require __DIR__ . '/src/actions/save_servico.php';
            break;

        default:
            http_response_code(404);
            echo 'Ação não encontrada.';
            break;
    }
    exit();
}

// =====================
// RENDERIZAÇÃO DE PÁGINAS
// =====================
$allowed_pages = ['login', 'dashboard', 'add_cliente', 'add_servico', 'diagnostico', 'alertas', 'ultimos_servicos'];
$page = $_GET['page'] ?? 'login';

// Forçar login quando necessário
if (!isset($_SESSION['user_id']) && $page !== 'login') {
    $page = 'login';
}
if (isset($_SESSION['user_id']) && $page === 'login') {
    $page = 'dashboard';
}
if (!in_array($page, $allowed_pages)) {
    $page = 'login';
}

// Cabeçalho comum
require __DIR__ . '/templates/partials/header.php';

// Conteúdo
switch ($page) {
    case 'dashboard':
        require __DIR__ . '/templates/dashboard.php';
        break;
    case 'add_cliente':
        require __DIR__ . '/templates/add_cliente_form.php';
        break;
    case 'add_servico':
        require __DIR__ . '/templates/add_servico_form.php';
        break;
    case 'diagnostico':
        require __DIR__ . '/templates/diagnostico.php';
        break;
    case 'alertas':
        require __DIR__ . '/templates/alertas.php';
        break;
    case 'ultimos_servicos':
        require __DIR__ . '/templates/ultimos_servicos.php';
        break;
    default:
        require __DIR__ . '/templates/login_form.php';
        break;
}

// Rodapé comum
require __DIR__ . '/templates/partials/footer.php';
