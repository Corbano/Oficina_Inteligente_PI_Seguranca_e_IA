<?php
// templates/add_servico_form.php
require_once __DIR__ . '/../src/core/security.php';

// Obtém os dados do formulário da sessão, se existirem (para o formulário "sticky")
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Limpa para não preencher de novo

// Obtém a data: ou a que foi submetida, ou a de hoje
$data_valor = htmlspecialchars($form_data['data'] ?? date('Y-m-d'));
?>

<!-- INÍCIO: CÓDIGO DA MENSAGEM FLASH (do seu formulário de cliente) -->
<?php
if (isset($_SESSION['flash_message'])) {
    $f = $_SESSION['flash_message'];
    echo '<div class="alert alert-' . htmlspecialchars($f['type']) . ' alert-dismissible fade show" role="alert" style="max-width: 900px; margin: 0 auto 20px auto;">';
    echo htmlspecialchars($f['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['flash_message']);
}
?>
<!-- FIM: CÓDIGO DA MENSAGEM FLASH -->

<div class="card shadow p-4" style="max-width: 900px; margin: 0 auto;">
    
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-tools"></i> Registar Serviço
    </h3>

    <form method="post" action="index.php?action=save_servico" autocomplete="off">
        
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <!-- BUSCA DE CLIENTE -->
        <div class="mb-3 position-relative">
            <label class="form-label fw-bold">Nome do Cliente *</label>
            <input 
                type="text"
                id="cliente_nome"
                name="cliente_nome"
                class="form-control"
                placeholder="Digite o nome do cliente..."
                autocomplete="off"
                required
                value="<?= htmlspecialchars($form_data['cliente_nome'] ?? '') ?>"
            >
            <div 
                id="sugestoes_clientes" 
                class="list-group position-absolute w-100" 
                style="z-index: 1000; display:none; max-height: 300px; overflow-y: auto;">
            </div>
        </div>

        <!-- CAMPOS AUTO-PREENCHÍVEIS (agora também "sticky") -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Telefone</label>
                <input type="text" class="form-control" id="cliente_telefone" name="cliente_telefone" 
                       value="<?= htmlspecialchars($form_data['cliente_telefone'] ?? '') ?>"
                       placeholder="(Preenchido automaticamente)">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Modelo do Carro</label>
                <input type="text" class="form-control" id="cliente_carro" name="cliente_carro" 
                       value="<?= htmlspecialchars($form_data['cliente_carro'] ?? '') ?>"
                       placeholder="(Preenchido automaticamente)">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Placa</label>
                <input type="text" class="form-control" id="cliente_placa" name="cliente_placa" 
                       value="<?= htmlspecialchars($form_data['cliente_placa'] ?? '') ?>"
                       placeholder="(Preenchido automaticamente)">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">KM *</label>
                <input type="number" class="form-control" name="km" 
                       value="<?= htmlspecialchars($form_data['km'] ?? '') ?>"
                       required>
            </div>
        </div>

        <!-- TIPO DE SERVIÇO -->
        <div class="mb-3">
            <label class="form-label fw-bold">Tipo de Serviço *</label>
            <select name="servico" class="form-select" required>
                <option value="">Selecione...</option>
                <?php
                $opcoes_servico = ['Troca de Óleo', 'Revisão', 'Troca de Pastilhas', 'Balanceamento', 'Alinhamento', 'Elétrica', 'Diagnóstico', 'Outro'];
                foreach ($opcoes_servico as $opcao) {
                    $selected = (isset($form_data['servico']) && $form_data['servico'] == $opcao) ? 'selected' : '';
                    echo "<option {$selected}>{$opcao}</option>";
                }
                ?>
            </select>
        </div>

        <!-- DATA / ESTADO / PAGAMENTO -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Data *</label>
                <input type="date" class="form-control" name="data" value="<?= $data_valor ?>" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Estado do Serviço *</label>
                <select name="status" class="form-select" required>
                    <?php
                    $opcoes_status = ['Em andamento', 'Concluído', 'Aguardando Peças'];
                    foreach ($opcoes_status as $opcao) {
                        $selected = (isset($form_data['status']) && $form_data['status'] == $opcao) ? 'selected' : '';
                        echo "<option {$selected}>{$opcao}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Forma de Pagamento</label>
                <select name="pagamento" class="form-select">
                    <?php
                    $opcoes_pag = ['Não informado', 'Dinheiro', 'PIX', 'Cartão Débito', 'Cartão Crédito'];
                    foreach ($opcoes_pag as $opcao) {
                        $selected = (isset($form_data['pagamento']) && $form_data['pagamento'] == $opcao) ? 'selected' : '';
                        echo "<option {$selected}>{$opcao}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- VALOR -->
        <div class="mb-3">
            <label class="form-label fw-bold">Valor (R$)</label>
            <input type="number" step="0.01" class="form-control" name="valor" 
                   value="<?= htmlspecialchars($form_data['valor'] ?? '') ?>"
                   placeholder="0.00">
        </div>

        <!-- OBSERVAÇÕES -->
        <div class="mb-3">
            <label class="form-label fw-bold">Observações</label>
            <textarea class="form-control" name="obs" rows="3" placeholder="Detalhes do serviço..."><?= htmlspecialchars($form_data['obs'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3 w-100 fw-bold">
            <i class="bi bi-check-circle"></i> Salvar Serviço
        </button>
    </form>
</div>

<!-- O JavaScript de Autocompletar continua o mesmo -->
<script>
document.getElementById("cliente_nome").addEventListener("keyup", function () {
    let termo = this.value.trim();
    let box = document.getElementById("sugestoes_clientes");

    // Se o utilizador está a digitar (e não a limpar), e o campo de telefone está vazio
    // (sugerindo que não selecionou um cliente), executamos o autocompletar.
    let telefonePreenchido = document.getElementById("cliente_telefone").value;
    if (termo.length < 2 || telefonePreenchido) {
        box.style.display = "none";
        return;
    }
    
    // Limpa os campos se o utilizador começar a digitar um novo nome
    document.getElementById("cliente_telefone").value = '';
    document.getElementById("cliente_carro").value = '';
    document.getElementById("cliente_placa").value = '';

    fetch("api/autocomplete.php?term=" + encodeURIComponent(termo))
        .then(res => res.json())
        .then(lista => {
            box.innerHTML = "";
            if (lista.length === 0) {
                box.style.display = "block";
                box.innerHTML = `<div class="list-group-item text-danger">Cliente não encontrado. <a href="index.php?page=add_cliente" target="_blank">Registar novo?</a></div>`;
                return;
            }

            lista.forEach(cliente => { 
                let nomeCliente = (typeof cliente === 'object') ? cliente.nome : cliente;
                
                let item = document.createElement("div");
                item.classList.add("list-group-item", "list-group-item-action");
                item.style.cursor = "pointer";
                item.textContent = nomeCliente; 

                item.onclick = () => {
                    document.getElementById("cliente_nome").value = nomeCliente; 
                    box.style.display = "none";

                    const url = "api/get_client_details.php?nome=" + encodeURIComponent(nomeCliente); 
                    fetch(url) 
                        .then(r => r.ok ? r.json() : Promise.reject('Erro de rede: ' + r.status))
                        .then(cli => { 
                            if (!cli.error) {
                                document.getElementById("cliente_telefone").value = cli.telefone || '';
                                document.getElementById("cliente_carro").value = cli.carro || ''; // Espera 'carro' do JSON
                                document.getElementById("cliente_placa").value = cli.placa || '';
                            }
                        })
                        .catch(error => console.error('[ERRO FATAL] Falha no Fetch ou JSON.parse:', error));
                };
                box.appendChild(item);
            });
            box.style.display = "block";
        })
        .catch(error => console.error('Erro no autocomplete:', error));
});

// Fechar sugestões ao clicar fora
document.addEventListener('click', function(e) {
    if (!e.target.closest('#sugestoes_clientes') && e.target.id !== 'cliente_nome') {
        document.getElementById('sugestoes_clientes').style.display = 'none';
    }
});
</script>

<style>
.list-group-item:hover {
    background-color: #f0f0f0;
}
</style>