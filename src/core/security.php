<?php
// ======================================================
// SECURITY.PHP  
// Sistema de proteção e funções de segurança
// ======================================================

// Garantir que a sessão esteja ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================================================
// GERAR TOKEN CSRF
// ======================================================
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ======================================================
// VALIDAR TOKEN CSRF
// ======================================================
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

// ======================================================
// SANITIZAÇÃO DE DADOS
// ======================================================
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ======================================================
// Proteção de acesso: exigir login
// ======================================================
function require_auth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit();
    }
}

// ======================================================
// FUNÇÃO e() — ESCAPE SEGURO PARA HTML
// ======================================================
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>
