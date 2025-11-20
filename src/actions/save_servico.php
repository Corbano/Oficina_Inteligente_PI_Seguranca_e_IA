<?php
// ======================================================
// SCRIPT DE AÇÃO (PARA SER INCLUÍDO PELO index.php)
// ======================================================

// O index.php já iniciou a sessão e carregou db.php e security.php
if (!isset($pdo)) {
    die("Erro fatal: A conexão PDO não foi inicializada pelo index.php");
}

// ======================================================
// VERIFICAÇÃO DO TOKEN CSRF
// ======================================================
if (
    !isset($_POST['csrf_token']) ||
    !function_exists('validate_csrf_token') ||
    !validate_csrf_token($_POST['csrf_token'])
) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Falha na validação de segurança. Tente novamente.'
    ];
    $_SESSION['form_data'] = $_POST; 
    header("Location: index.php?page=add_servico");
    exit;
}

// ======================================================
// REQUISIÇÃO PRECISA SER POST
// ======================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método não permitido. Utilize POST.");
}

// ======================================================
// VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS
// ======================================================
// O formulário envia 'servico', mas o código usa a variável $servico
$required_fields = ['cliente_nome', 'servico', 'km', 'data']; 

foreach ($required_fields as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => "Preencha todos os campos obrigatórios! (Campo: $field)"
        ];
        $_SESSION['form_data'] = $_POST; 
        header("Location: index.php?page=add_servico");
        exit;
    }
}

// ======================================================
// SANITIZAÇÃO DOS DADOS
// ======================================================
$cliente_nome = sanitize_input($_POST['cliente_nome']);
$tipo_servico = sanitize_input($_POST['servico']); // Renomeado para clareza
$quilometragem = intval($_POST['km']);           // Renomeado para clareza
$data_servico = sanitize_input($_POST['data']);
$telefone = sanitize_input($_POST['cliente_telefone'] ?? '');
$modelo_carro = sanitize_input($_POST['cliente_carro'] ?? ''); 
$placa = sanitize_input($_POST['cliente_placa'] ?? '');

// Dados que não vão para a tabela 'servicos', mas que o form envia
$status = sanitize_input($_POST['status']);
$pagamento = sanitize_input($_POST['pagamento'] ?? 'Não informado');
$valor = floatval($_POST['valor'] ?? 0);
$obs = sanitize_input($_POST['obs'] ?? '');


// ======================================================
// PROCESSAMENTO (SALVAR NO BANCO)
// ======================================================
try {
    $pdo->beginTransaction();

    // 1. BUSCAR OU CRIAR CLIENTE
    $stmt_cliente = $pdo->prepare("SELECT id FROM clientes WHERE nome = ?");
    $stmt_cliente->execute([$cliente_nome]);
    $cliente = $stmt_cliente->fetch();

    if (!$cliente) {
        $stmt_novo_cliente = $pdo->prepare("
            INSERT INTO clientes (nome, telefone, data_cadastro) 
            VALUES (?, ?, NOW())
        ");
        $stmt_novo_cliente->execute([$cliente_nome, $telefone]);
        $cliente_id = $pdo->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
        if (!empty($telefone)) {
            $stmt_update_cliente = $pdo->prepare("
                UPDATE clientes 
                SET telefone = COALESCE(NULLIF(?, ''), telefone)
                WHERE id = ?
            ");
            $stmt_update_cliente->execute([$telefone, $cliente_id]);
        }
    }

    // 2. BUSCAR OU CRIAR VEÍCULO E GUARDAR O ID
    $veiculo_id_para_servico = null; // Inicia a variável

    if (!empty($placa) || !empty($modelo_carro)) {
        
        $sql_find_veiculo = "SELECT id FROM veiculos WHERE cliente_id = ?";
        $params_find_veiculo = [$cliente_id];

        if (!empty($placa)) {
            $sql_find_veiculo .= " AND placa = ?";
            $params_find_veiculo[] = $placa;
        } else {
             $sql_find_veiculo .= " AND modelo = ?";
             $params_find_veiculo[] = $modelo_carro;
        }

        $stmt_veiculo = $pdo->prepare($sql_find_veiculo . " LIMIT 1");
        $stmt_veiculo->execute($params_find_veiculo);
        $veiculo = $stmt_veiculo->fetch();

        if (!$veiculo) {
            $stmt_novo_veiculo = $pdo->prepare("
                INSERT INTO veiculos (cliente_id, modelo, placa) 
                VALUES (?, ?, ?)
            ");
            $stmt_novo_veiculo->execute([$cliente_id, $modelo_carro, $placa]);
            $veiculo_id_para_servico = $pdo->lastInsertId(); // Pega o ID do NOVO veículo
        } else {
            $veiculo_id_para_servico = $veiculo['id']; // Pega o ID do veículo EXISTENTE
        }
    }


    // 3. INSERIR O SERVIÇO (AGORA CORRETO)
    // A consulta agora corresponde exatamente à sua tabela
    $stmt_servico = $pdo->prepare("
        INSERT INTO servicos 
        (cliente_id, veiculo_id, tipo_servico, data_servico, quilometragem)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt_servico->execute([
        $cliente_id,
        $veiculo_id_para_servico, // A ponte para o veículo
        $tipo_servico,
        $data_servico,
        $quilometragem
    ]);

    $pdo->commit();

    $_SESSION['flash_message'] = [
        'type' => 'success', 
        'message' => 'Serviço cadastrado com sucesso!'
    ];
    
    unset($_SESSION['form_data']); 

    header("Location: index.php?page=dashboard");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao salvar serviço: " . $e->getMessage());
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => "Erro ao salvar o serviço: " . $e->getMessage()
    ];
    $_SESSION['form_data'] = $_POST; 
    header("Location: index.php?page=add_servico");
    exit;
}
?>