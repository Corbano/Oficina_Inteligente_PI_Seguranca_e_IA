<?php
// src/actions/servicos/salvar_servico.php
// Recebe POST do add_servico_form.php e salva o serviço
// Ajuste os caminhos conforme sua estrutura

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/core/security.php';

// Verifica método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido');
}

// CSRF
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    $_SESSION['flash_message'] = ['type'=>'danger','message'=>'Falha na validação CSRF.'];
    header('Location: index.php?page=add_servico');
    exit;
}

// Campos esperados
$cliente_id = $_POST['cliente_id'] ?? null;
$veiculo_placa = trim($_POST['veiculo_placa'] ?? '');
$veiculo_modelo = trim($_POST['veiculo_modelo'] ?? '');
$tipo_servico = trim($_POST['servico'] ?? '');
$data_servico = $_POST['data'] ?? null;
$quilometragem = $_POST['km'] ?? null;
$status = $_POST['status'] ?? 'Em andamento';
$valor = $_POST['valor'] ?? null;
$obs = trim($_POST['obs'] ?? '');

try {
    // validações basicas
    if (empty($tipo_servico) || empty($data_servico) || $quilometragem === null) {
        throw new Exception('Preencha todos os campos obrigatórios.');
    }

    // Se cliente não foi selecionado, redireciona ao cadastro (defesa adicional)
    if (empty($cliente_id)) {
        $_SESSION['flash_message'] = ['type'=>'warning','message'=>'Cliente não selecionado. Cadastre o cliente antes de registrar o serviço.'];
        header('Location: index.php?page=add_cliente');
        exit;
    }

    $pdo->beginTransaction();

    // verifica se existe veículo com essa placa para esse cliente
    $veiculo_id = null;
    if (!empty($veiculo_placa)) {
        $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE cliente_id = ? AND placa = ? LIMIT 1");
        $stmt->execute([$cliente_id, $veiculo_placa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $veiculo_id = $row['id'];
        }
    }

    // se não existe veículo (ou placa não informada), tenta buscar qualquer veículo do cliente (primário)
    if (!$veiculo_id) {
        $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE cliente_id = ? LIMIT 1");
        $stmt->execute([$cliente_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $veiculo_id = $row['id'];
        }
    }

    // se ainda não existe veículo, e temos placa/modelo, insere novo veículo
    if (!$veiculo_id && ($veiculo_placa || $veiculo_modelo)) {
        $stmt = $pdo->prepare("INSERT INTO veiculos (cliente_id, placa, modelo) VALUES (?, ?, ?)");
        $stmt->execute([$cliente_id, $veiculo_placa, $veiculo_modelo]);
        $veiculo_id = $pdo->lastInsertId();
    }

    if (!$veiculo_id) {
        // não conseguimos associar veículo
        throw new Exception('Veículo não encontrado e não foi possível criar um veículo automaticamente. Cadastre o veículo para esse cliente.');
    }

    // insere serviço
    $stmt = $pdo->prepare("
        INSERT INTO servicos 
            (veiculo_id, tipo_servico, data_servico, quilometragem, valor, obs, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $veiculo_id,
        $tipo_servico,
        $data_servico,
        $quilometragem,
        $valor !== '' ? $valor : null,
        $obs,
        $status
    ]);

    $pdo->commit();

    $_SESSION['flash_message'] = ['type'=>'success','message'=>'Serviço salvo com sucesso!'];
    header('Location: index.php?page=dashboard');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Erro ao salvar serviço: ' . $e->getMessage());
    $_SESSION['flash_message'] = ['type'=>'danger','message'=>'Erro ao salvar os dados: ' . $e->getMessage()];
    header('Location: index.php?page=add_servico');
    exit;
}
