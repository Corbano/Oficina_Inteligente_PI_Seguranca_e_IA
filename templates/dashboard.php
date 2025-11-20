<?php
require __DIR__ . '/../src/auth/auth_checker.php';
require __DIR__ . '/../config/db.php';

// Função segura para imprimir dados no HTML
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

// Capturar e limpar mensagem flash
$flash_message = null;
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Contar alertas de manutenção para exibir no card
try {
    $fiveMonthsAgo = new DateTime();
    $fiveMonthsAgo->modify('-5 months');

    $query = $pdo->prepare("
        SELECT COUNT(*) as total_alertas
        FROM servicos s
        JOIN veiculos v ON s.veiculo_id = v.id
        WHERE 
            s.tipo_servico = 'Troca de Óleo'
            AND s.data_servico = (
                SELECT MAX(s2.data_servico)
                FROM servicos s2
                WHERE s2.veiculo_id = s.veiculo_id
                AND s2.tipo_servico = 'Troca de Óleo'
            )
            AND s.data_servico <= :fiveMonthsAgo
    ");
    $query->execute([':fiveMonthsAgo' => $fiveMonthsAgo->format('Y-m-d')]);
    $resultado = $query->fetch(PDO::FETCH_ASSOC);
    $total_alertas = $resultado ? $resultado['total_alertas'] : 0;
} catch (Exception $e) {
    $total_alertas = 0;
}
?>

<h1 class="mb-4 text-bic-blue">Dashboard</h1>

<?php if ($flash_message): ?>
    <div class="alert alert-<?php echo e($flash_message['type']); ?> alert-dismissible fade show" role="alert">
        <?php echo e($flash_message['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- CONSULTA DE CLIENTES -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-bic-blue"><i class="bi bi-search"></i> Consultar Histórico do Cliente</h5>
                <p class="card-text text-muted">Digite o nome do cliente para ver seu histórico de serviços.</p>
                
                <!-- Autocomplete -->
                <div class="position-relative">
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Digite 2 ou mais letras..."
                           autocomplete="off">
                    <div id="suggestionsBox" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                </div>
                
                <!-- NOVO: Área de Resultados do Histórico -->
                <div id="historyResultBox" class="mt-4" style="max-height: 500px; overflow-y: auto;">
                    <!-- O histórico aparecerá aqui -->
                </div>
                
            </div>
        </div>
    </div>
</div>

<hr class="my-4">

<!-- ACESSO RÁPIDO -->
<div class="row">
    <div class="col-12">
        <h2 class="h4 mb-3"><i class="bi bi-speedometer2"></i> Acesso Rápido</h2>
    </div>

    <!-- Card 1: Adicionar Cliente -->
    <div class="col-md-4 mb-4">
        <a href="index.php?page=add_cliente" class="nav-card">
            <div class="card shadow-sm h-100 bg-mustard-yellow">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-person-plus-fill text-white" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3 mb-0 text-white fw-bold">Adicionar Novo Cliente</h5>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Diagnóstico -->
    <div class="col-md-4 mb-4">
        <a href="index.php?page=diagnostico" class="nav-card">
            <div class="card shadow-sm h-100 bg-bic-blue">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-cpu-fill text-white" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3 mb-0 text-white fw-bold">Diagnóstico de Veículo por IA</h5>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Registrar Serviço -->
    <div class="col-md-4 mb-4">
        <a href="index.php?page=add_servico" class="nav-card">
            <div class="card shadow-sm h-100 bg-mustard-yellow">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-card-list text-white" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3 mb-0 text-white fw-bold">Registrar Novo Serviço</h5>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: Alertas de Manutenção (com contador) -->
    <div class="col-md-4 mb-4">
        <a href="index.php?page=alertas" class="nav-card">
            <div class="card shadow-sm h-100 bg-warning">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-bell-fill text-white" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3 mb-1 text-white fw-bold">Alertas de Manutenção</h5>
                    <p class="text-white mb-0 fw-bold" style="font-size:1.2rem;">
                        <?php echo $total_alertas; ?> alertas
                    </p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 5: Últimos Serviços -->
    <div class="col-md-4 mb-4">
        <a href="index.php?page=ultimos_servicos" class="nav-card">
            <div class="card shadow-sm h-100 bg-info">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-card-list text-white" style="font-size: 3rem;"></i>
                    <h5 class="card-title mt-3 mb-0 text-white fw-bold">Últimos Serviços</h5>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Card 6: Importar CSV (REMOVIDO) -->

</div>

<!-- MODAL DE CLIENTE (Oculto, mas ainda aqui caso precise) -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-bic-blue text-white">
                <h5 class="modal-title"><i class="bi bi-person-circle"></i> Detalhes do Cliente</h5>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do modal será preenchido via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- NOVO: JavaScript para o Histórico de Serviços -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('suggestionsBox');
    const historyResultBox = document.getElementById('historyResultBox');

    // 1. Autocomplete (usa o autocomplete.php que já existe)
    searchInput.addEventListener('keyup', function() {
        const termo = this.value.trim();
        
        if (termo.length < 2) {
            suggestionsBox.style.display = 'none';
            suggestionsBox.innerHTML = '';
            return;
        }

        fetch(`api/autocomplete.php?term=${encodeURIComponent(termo)}`)
            .then(res => res.json())
            .then(lista => {
                suggestionsBox.innerHTML = '';
                if (lista.length === 0) {
                    suggestionsBox.innerHTML = '<div class="list-group-item text-danger">Nenhum cliente encontrado.</div>';
                    suggestionsBox.style.display = 'block';
                    return;
                }

                lista.forEach(nomeCliente => {
                    const item = document.createElement('div');
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.style.cursor = 'pointer';
                    item.textContent = nomeCliente;
                    
                    // Ação de clique: buscar o histórico
                    item.onclick = () => {
                        searchInput.value = nomeCliente; // Preenche o input
                        suggestionsBox.style.display = 'none'; // Esconde sugestões
                        suggestionsBox.innerHTML = '';
                        loadHistory(nomeCliente); // CHAMA A FUNÇÃO DE BUSCAR HISTÓRICO
                    };
                    
                    suggestionsBox.appendChild(item);
                });
                suggestionsBox.style.display = 'block';
            })
            .catch(error => console.error('Erro no autocomplete:', error));
    });

    // 2. Função para buscar e exibir o histórico (usa a NOVA api/get_history.php)
    function loadHistory(clientName) {
        historyResultBox.innerHTML = '<div class="text-center p-3"><span class="spinner-border spinner-border-sm"></span> Carregando histórico...</div>';

        fetch(`api/get_history.php?nome=${encodeURIComponent(clientName)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    historyResultBox.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    return;
                }
                
                if (data.servicos.length === 0) {
                    historyResultBox.innerHTML = `<div class="alert alert-secondary">Nenhum serviço encontrado para <strong>${e(clientName)}</strong>.</div>`;
                    return;
                }
                
                // Constrói o HTML do histórico
                let html = `
                    <h5 class="text-muted">Histórico de ${e(data.cliente.nome)}</h5>
                    <p>Telefone: ${e(data.cliente.telefone) || 'Não informado'}</p>
                    <hr>
                    <ul class="list-group">
                `;
                
                data.servicos.forEach(s => {
                    html += `
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${e(s.tipo_servico)}</h6>
                                <small class="text-muted">${new Date(s.data_servico + 'T00:00:00').toLocaleDateString('pt-BR', {timeZone: 'UTC'})}</small>
                            </div>
                            <p class="mb-1">
                                Veículo: <strong>${e(s.modelo) || 'Não informado'}</strong> 
                                <span class="badge bg-dark">${e(s.placa) || 'Sem placa'}</span>
                            </p>
                            <small class="text-muted">KM: ${e(s.quilometragem)}</small>
                        </li>
                    `;
                });
                
                html += '</ul>';
                historyResultBox.innerHTML = html;
            })
            .catch(error => {
                console.error('Erro ao buscar histórico:', error);
                historyResultBox.innerHTML = `<div class="alert alert-danger">Erro de conexão ao buscar o histórico.</div>`;
            });
    }

    // Função helper para escapar HTML no JS
    function e(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    // Fechar sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#suggestionsBox') && e.target.id !== 'searchInput') {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>

<style>
/* Estilo para os cards de navegação */
.nav-card {
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: block;
}
.nav-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
}
/* Cor da borda do dashboard (que já tinha) */
.border-left-bic-blue {
    border-left: 4px solid #004a98; 
}
.bg-bic-blue {
    background-color: #004a98;
}
.text-bic-blue {
    color: #004a98;
}
.bg-mustard-yellow {
    background-color: #f7b733; 
}
</style>