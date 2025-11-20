<?php
// ======================================================
// DEBUG – MOSTRAR ERROS (remover em produção)
// ======================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ======================================================
// INICIAR SESSÃO
// ======================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================================================
// INCLUDES CORRETOS
// ======================================================
require_once __DIR__ . '/../../config/db.php';   // banco
require_once __DIR__ . '/../core/security.php';  // CORRIGIDO

// ======================================================
// VALIDAR TOKEN CSRF
// ======================================================
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    $_SESSION['flash_message'] = [
        'type'    => 'danger',
        'message' => 'Falha na validação do token CSRF.'
    ];
    header('Location: index.php?page=add_cliente');
    exit;
}

// ======================================================
// PROCESSA SOMENTE POST
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // -----------------------------
        // CAMPOS OBRIGATÓRIOS
        // -----------------------------
        $required = ['nome', 'telefone', 'placa', 'modelo'];

        foreach ($required as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                throw new Exception("Preencha o campo obrigatório: {$field}");
            }
        }

        // Sanitização
        $nome     = sanitize_input($_POST['nome']);
        $telefone = sanitize_input($_POST['telefone']);
        $placa    = strtoupper(sanitize_input($_POST['placa'])); // sempre maiúsculas
        $modelo   = sanitize_input($_POST['modelo']);

        // ======================================================
        // INICIAR TRANSAÇÃO
        // ======================================================
        $pdo->beginTransaction();

        // ======================================================
        // VERIFICAR PLACA DUPLICADA
        // ======================================================
        $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE placa = ?");
        $stmt->execute([$placa]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("A placa <strong>{$placa}</strong> já está cadastrada.");
        }

        // ======================================================
        // VERIFICAR TELEFONE DUPLICADO
        // ======================================================
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE telefone = ?");
        $stmt->execute([$telefone]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("Este cliente já está cadastrado (telefone duplicado).");
        }

        // ======================================================
        // INSERIR CLIENTE
        // ======================================================
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone) VALUES (?, ?)");
        $stmt->execute([$nome, $telefone]);
        $cliente_id = $pdo->lastInsertId();

        // ======================================================
        // INSERIR VEÍCULO
        // ======================================================
        $stmt = $pdo->prepare("INSERT INTO veiculos (cliente_id, placa, modelo) VALUES (?, ?, ?)");
        $stmt->execute([$cliente_id, $placa, $modelo]);

        // ======================================================
        // FINALIZAR
        // ======================================================
        $pdo->commit();

        $_SESSION['flash_message'] = [
            'type'    => 'success',
            'message' => 'Cliente e veículo cadastrados com sucesso!'
        ];

        header('Location: index.php?page=dashboard');
        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log("ERRO AO SALVAR CLIENTE: " . $e->getMessage());

        $_SESSION['flash_message'] = [
            'type'    => 'danger',
            'message' => "Erro ao salvar: " . $e->getMessage()
        ];

        header('Location: index.php?page=add_cliente');
        exit;
    }
}

die("Método inválido.");
?>
