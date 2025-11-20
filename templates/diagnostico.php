<?php
// =============================================
// = INÍCIO: FORÇAR EXIBIÇÃO DE ERROS (DEBUG) =
// =============================================
// Estas 3 linhas forçam o PHP a mostrar o erro em vez de uma tela branca ou redirecionamento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =============================================
// = FIM: FORÇAR EXIBIÇÃO DE ERROS (DEBUG)   =
// =============================================

// 1. AUTENTICAÇÃO: Agora, esta linha vai mostrar um erro se falhar
require __DIR__ . '/../src/auth/auth_checker.php'; 

// Se o código chegar aqui, a autenticação passou.
// Vamos verificar o resto...

// =========================================================
// =     INÍCIO: LÓGICA PHP - CENTRAL DE DIAGNÓSTICO     =
// =========================================================

// Inicializa as variáveis de resultado
$diagnostico_ia = "";
$diagnostico_classe = ""; // Para a cor do alerta (Bootstrap)
$resultado_ia = []; // Guarda o JSON completo da API

// Verifica se o módulo cURL está instalado (causa comum de página em branco)
if (!function_exists('curl_init')) {
    $diagnostico_ia = "Erro Fatal: O módulo PHP cURL não está instalado ou ativado no servidor. A API não pode ser contatada.";
    $diagnostico_classe = 'alert-danger';
} 
// 2. Verifica se o formulário foi enviado
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Define a URL da sua API
    $url_api = "http://oficina_ai.nexusprime22.com.br/prever";

    // 4. Coleta dados do formulário (com o novo campo 'ano')
    $dadosParaAPI = [
        'carro' => $_POST['carro'],
        'ano'   => (int)$_POST['ano'],
        'km'    => (int)$_POST['km'], 
        'frequencia_uso' => $_POST['frequencia_uso'] // CAMPO ATUALIZADO
    ];

    // 5. Prepara a chamada cURL
    $payload = json_encode($dadosParaAPI);
    $ch = curl_init($url_api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    // 6. Executa e pega a resposta
    $resposta_json = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch);

    // 7. Decodifica e trata a resposta
    $resultado_ia = json_decode($resposta_json, true); 

    if ($http_code == 200 && isset($resultado_ia['resultado'])) {
        $diagnostico_ia = $resultado_ia['resultado']; // Mensagem principal
        
        // Define a cor do card
        if (strpos($diagnostico_ia, '⚠️') !== false) {
            $diagnostico_classe = 'alert-warning';
        } else {
            $diagnostico_classe = 'alert-success';
        }

    } elseif (isset($resultado_ia['erro'])) {
        $diagnostico_ia = "Erro da API: " . $resultado_ia['erro'];
        $diagnostico_classe = 'alert-danger';
    } else {
        $diagnostico_ia = "Erro: Não foi possível conectar à API de diagnóstico (Código: $http_code).";
        $diagnostico_classe = 'alert-danger';
    }
}
// =========================================================
// =      FIM: LÓGICA PHP - CENTRAL DE DIAGNÓSTICO       =
// =========================================================
?>

<h1 class="mb-4 text-bic-blue"><i class="bi bi-cpu-fill"></i> Central de Diagnóstico (IA)</h1>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-bic-blue">Gerar Novo Diagnóstico</h5>
                <p class="card-text text-muted">Preencha os dados do veículo para obter um diagnóstico preditivo sobre a saúde do motor, baseado em nosso modelo de IA.</p>
                
                <form method="POST" action="index.php?page=diagnostico">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="carro" class="form-label fw-bold">Veículo (Modelo)</label>
                            <input type="text" id="carro" name="carro" class="form-control" placeholder="Ex: Fusca" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="ano" class="form-label fw-bold">Ano Fabricação</label>
                            <input type="number" id="ano" name="ano" class="form-control" placeholder="Ex: 2015" min="1950" max="2026" required>
                        </div>

                        <div class="col-md-3">
                            <label for="km" class="form-label fw-bold">Quilometragem</label>
                            <input type="number" id="km" name="km" class="form-control" placeholder="Ex: 160000" required>
                        </div>

                        <div class="col-md-3">
                            <label for="frequencia_uso" class="form-label fw-bold">Frequência de Uso</label>
                            <select id="frequencia_uso" name="frequencia_uso" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="diario">Uso Diário</option>
                                <option value="regular">Uso Regular (2-3x p/ semana)</option>
                                <option value="raro">Uso Raro (Menos de 4x p/ mês)</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn bg-bic-blue text-white mt-3">
                        <i class="bi bi-cpu-fill"></i> Analisar Agora
                    </button>
                </form>

                <?php
                // 8. Se a variável $diagnostico_ia tiver algo (do POST), mostre o resultado
                if (!empty($diagnostico_ia)): 

                    // Busca a lista de inspeção (ou um array vazio se não existir)
                    $itens_inspecao = $resultado_ia['itens_inspecao'] ?? []; 
                ?>
                    <hr class="my-4">
                    <h5 class="text-bic-blue">Resultado da Análise:</h5>
                    
                    <div class="alert <?php echo $diagnostico_classe; ?> d-flex mb-0" role="alert">
                        
                        <?php if ($diagnostico_classe == 'alert-success'): ?>
                            <i class="bi bi-check-circle-fill flex-shrink-0 me-2" style="font-size: 1.5rem;"></i>
                        <?php else: ?>
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" style="font-size: 1.5rem;"></i>
                        <?php endif; ?>

                        <div class="fw-bold">
                            <?php echo htmlspecialchars($diagnostico_ia); ?>
                        </div>
                    </div>

                    <?php if (!empty($itens_inspecao)): ?>
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-white fw-bold">
                                <i class="bi bi-list-check"></i> Itens Recomendados para Inspeção:
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($itens_inspecao as $item): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div> </div> </div> </div> 

