<?php
// add_cliente_form.php

// Exibe mensagens flash, caso existam
if (isset($_SESSION['flash_message'])) {
    $f = $_SESSION['flash_message'];
    echo '<div class="alert alert-' . htmlspecialchars($f['type']) . ' mb-3">'
        . htmlspecialchars($f['message']) .
    '</div>';
    unset($_SESSION['flash_message']);
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-bic-blue text-white">
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Cadastrar Novo Cliente</h5>
    </div>

    <div class="card-body">

        <form method="post" action="index.php?action=save_cliente" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

            <div class="mb-3">
                <label class="form-label">Nome *</label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    class="form-control"
                    required
                    value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Telefone *</label>
                <input 
                    type="text" 
                    id="telefone" 
                    name="telefone" 
                    class="form-control"
                    required
                    value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Placa *</label>
                <input 
                    type="text" 
                    id="placa" 
                    name="placa" 
                    class="form-control"
                    required
                    value="<?php echo htmlspecialchars($_POST['placa'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Modelo *</label>
                <input 
                    type="text" 
                    id="modelo" 
                    name="modelo" 
                    class="form-control"
                    required
                    value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn text-white bg-bic-blue px-4">Salvar Cliente</button>
                <a href="index.php?page=dashboard" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

    </div>
</div>
