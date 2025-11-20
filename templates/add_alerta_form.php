<?php
// add_alerta_form.php (local: /template/)
include_once __DIR__ . "/../config/db.php";
?>

<div class="card shadow p-4" style="max-width: 900px; margin: 0 auto;">
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-bell"></i> Criar Alerta de Manutenção
    </h3>

    <form action="src/actions/alertas/salvar_alerta.php" method="POST">

        <!-- BUSCA DE CLIENTE COM AUTOCOMPLETE -->
        <div class="mb-3 position-relative">
            <label class="form-label fw-bold">Buscar Cliente *</label>
            <input 
                type="text" 
                id="cliente_busca" 
                class="form-control" 
                placeholder="Digite 2 ou mais letras do nome..."
                autocomplete="off"
                required
            >
            <div id="sugestoes_clientes" class="list-group position-absolute w-100" 
                 style="z-index:1000; display:none;"></div>
        </div>

        <!-- CAMPOS AUTOMÁTICOS -->
        <input type="hidden" name="cliente_id" id="cliente_id">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nome do Cliente</label>
                <input type="text" class="form-control bg-light" id="cliente_nome" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Carro</label>
                <input type="text" class="form-control bg-light" id="cliente_carro" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Telefone</label>
                <input type="text" class="form-control bg-light" id="cliente_telefone" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Placa</label>
                <input type="text" class="form-control bg-light" id="cliente_placa" readonly>
            </div>
        </div>

        <!-- MENSAGEM DO ALERTA -->
        <div class="mb-3">
            <label class="form-label fw-bold">Mensagem do Alerta *</label>
            <textarea class="form-control" name="mensagem" rows="3" required></textarea>
        </div>

        <button class="btn btn-primary mt-3 w-100 fw-bold">
            <i class="bi bi-check-circle"></i> Salvar Alerta
        </button>

    </form>
</div>

<script>
// AUTOCOMPLETE
document.getElementById("cliente_busca").addEventListener("keyup", function () {
    let termo = this.value.trim();

    if (termo.length < 2) {
        document.getElementById("sugestoes_clientes").style.display = "none";
        return;
    }

    fetch("src/actions/clientes/buscar_clientes.php?termo=" + termo)
    .then(res => res.json())
    .then(dados => {
        let box = document.getElementById("sugestoes_clientes");
        box.innerHTML = "";

        if (dados.length === 0) {
            box.style.display = "block";
            box.innerHTML = `
                <div class="p-2 text-danger">
                    Cliente não encontrado.<br>
                    <a href="index.php?page=add_cliente" class="btn btn-sm btn-warning mt-2">Cadastrar</a>
                </div>`;
            return;
        }

        dados.forEach(item => {
            let div = document.createElement("div");
            div.classList.add("list-group-item", "list-group-item-action");
            div.style.cursor = "pointer";

            div.innerHTML = `
                <strong>${item.nome}</strong> — ${item.carro} 
                <br><small>${item.telefone}</small>
            `;

            div.onclick = function () {
                document.getElementById("cliente_id").value = item.id;
                document.getElementById("cliente_nome").value = item.nome;
                document.getElementById("cliente_carro").value = item.carro;
                document.getElementById("cliente_telefone").value = item.telefone;
                document.getElementById("cliente_placa").value = item.placa;

                document.getElementById("cliente_busca").value = item.nome;
                box.style.display = "none";
            };

            box.appendChild(div);
        });

        box.style.display = "block";
    });
});
</script>

<style>
.list-group-item:hover {
    background: #eef2ff;
}
</style>
