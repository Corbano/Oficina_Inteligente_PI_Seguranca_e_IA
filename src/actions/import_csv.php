<?php
// src/actions/import_csv.php

// O index.php já iniciou a sessão e carregou db.php e security.php
if (!isset($pdo)) {
    die("Erro fatal: A conexão PDO não foi inicializada pelo index.php");
}

// ======================================================
// VERIFICAÇÃO CSRF E MÉTODO
// ======================================================
if (!isset($_POST['csrf_token']) || !function_exists('validate_csrf_token') || !validate_csrf_token($_POST['csrf_token'])) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Falha na validação de segurança.'];
    header("Location: index.php?page=import_csv");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método não permitido.");
}

// ======================================================
// VERIFICAÇÃO DO UPLOAD DO FICHEIRO
// ======================================================
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => 'Erro no upload do ficheiro. Tente novamente.'];
    header("Location: index.php?page=import_csv");
    exit;
}

$file_tmp_path = $_FILES['csv_file']['tmp_name'];

// ======================================================
// PROCESSAMENTO DO CSV
// ======================================================
$total_linhas = 0;
$clientes_novos = 0;
$clientes_atualizados = 0;
$veiculos_novos = 0;
$veiculos_atualizados = 0;
$linhas_ignoradas = 0;

try {
    $pdo->beginTransaction();

    if (($handle = fopen($file_tmp_path, "r")) !== FALSE) {
        
        // Ignorar a primeira linha (cabeçalho)
        fgetcsv($handle, 1000, ","); 
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $total_linhas++;

            // Sanitizar dados (assumindo a ordem: Nome, Telefone, Placa, Modelo)
            $nome = sanitize_input($data[0] ?? '');
            $telefone = sanitize_input($data[1] ?? '');
            $placa = sanitize_input($data[2] ?? '');
            $modelo = sanitize_input($data[3] ?? '');

            // Validar dados mínimos
            if (empty($nome) || (empty($placa) && empty($modelo))) {
                $linhas_ignoradas++;
                continue; // Pula esta linha se não tiver nome ou dados do veículo
            }
            
            // --- Lógica copiada do save_servico.php (Consistência) ---
            
            // 1. BUSCAR OU CRIAR CLIENTE
            $stmt_cliente = $pdo->prepare("SELECT id FROM clientes WHERE nome = ?");
            $stmt_cliente->execute([$nome]);
            $cliente = $stmt_cliente->fetch();

            if (!$cliente) {
                $stmt_novo_cliente = $pdo->prepare("INSERT INTO clientes (nome, telefone, data_cadastro) VALUES (?, ?, NOW())");
                $stmt_novo_cliente->execute([$nome, $telefone]);
                $cliente_id = $pdo->lastInsertId();
                $clientes_novos++;
            } else {
                $cliente_id = $cliente['id'];
                if (!empty($telefone)) {
                    $stmt_update_cliente = $pdo->prepare("UPDATE clientes SET telefone = COALESCE(NULLIF(?, ''), telefone) WHERE id = ?");
                    $stmt_update_cliente->execute([$telefone, $cliente_id]);
                    $clientes_atualizados++;
                }
            }

            // 2. BUSCAR OU CRIAR VEÍCULO (Lógica da Placa)
            if (!empty($placa)) {
                $stmt_veiculo = $pdo->prepare("SELECT id, cliente_id FROM veiculos WHERE placa = ? LIMIT 1");
                $stmt_veiculo->execute([$placa]);
                $veiculo = $stmt_veiculo->fetch();

                if ($veiculo) {
                    $veiculo_id = $veiculo['id'];
                    // Atualiza dono (se mudou) e modelo
                    $stmt_update_veiculo = $pdo->prepare("UPDATE veiculos SET cliente_id = ?, modelo = ? WHERE id = ?");
                    $stmt_update_veiculo->execute([$cliente_id, $modelo, $veiculo_id]);
                    $veiculos_atualizados++;
                } else {
                    $stmt_novo_veiculo = $pdo->prepare("INSERT INTO veiculos (cliente_id, modelo, placa) VALUES (?, ?, ?)");
                    $stmt_novo_veiculo->execute([$cliente_id, $modelo, $placa]);
                    $veiculos_novos++;
                }
            } else if (!empty($modelo)) {
                // Se não tem placa, apenas modelo (cria um genérico)
                $stmt_novo_veiculo = $pdo->prepare("INSERT INTO veiculos (cliente_id, modelo, placa) VALUES (?, ?, NULL)");
                $stmt_novo_veiculo->execute([$cliente_id, $modelo]);
                $veiculos_novos++;
            }
            // --- Fim da lógica ---
        }
        fclose($handle);
    }

    $pdo->commit();

    // Mensagem de sucesso detalhada
    $mensagem_sucesso = "Importação concluída!\n";
    $mensagem_sucesso .= "Total de linhas processadas: $total_linhas\n";
    $mensagem_sucesso .= "Clientes novos: $clientes_novos\n";
    $mensagem_sucesso .= "Clientes atualizados: $clientes_atualizados\n";
    $mensagem_sucesso .= "Veículos novos: $veiculos_novos\n";
    $mensagem_sucesso .= "Veículos atualizados: $veiculos_atualizados\n";
    $mensagem_sucesso .= "Linhas ignoradas (dados inválidos): $linhas_ignoradas";
    
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => $mensagem_sucesso];
    header("Location: index.php?page=import_csv");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao importar CSV: " . $e->getMessage());
    $_SESSION['flash_message'] = ['type' => 'danger', 'message' => "Erro ao importar o ficheiro: " . $e->getMessage()];
    header("Location: index.php?page=import_csv");
    exit;
}
?>