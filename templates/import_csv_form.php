<?php
// templates/import_csv_form.php
require_once __DIR__ . '/../src/core/security.php';

// Exibe mensagens flash, caso existam
if (isset($_SESSION['flash_message'])) {
    $f = $_SESSION['flash_message'];
    echo '<div class="alert alert-' . htmlspecialchars($f['type']) . ' alert-dismissible fade show" role="alert">';
    echo nl2br(htmlspecialchars($f['message'])); // nl2br para quebras de linha
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['flash_message']);
}
?>

<div class="card shadow p-4" style="max-width: 900px; margin: 0 auto;">
    
    <h3 class="mb-4 text-primary fw-bold">
        <i class="bi bi-cloud-upload"></i> Importar Clientes de CSV
    </h3>

    <div class="alert alert-info">
        <h5 class="fw-bold">Instruções Importantes</h5>
        <ol>
            <li>O seu ficheiro deve ser um <strong>CSV</strong> (separado por vírgulas). Se usa Excel, clique em "Salvar Como" e escolha "CSV (Delimitado por vírgulas)".</li>
            <li>A <strong>primeira linha</strong> do seu ficheiro deve ser o cabeçalho e será <strong>ignorada</strong>.</li>
            <li>A ordem das colunas deve ser <strong>exatamente</strong>:
                <br>
                <code>Nome, Telefone, Placa, Modelo</code>
            </li>
            <li>O script irá verificar a <strong>Placa</strong>. Se a placa já existir, ele irá atualizar o modelo do carro e associá-lo ao cliente (nome/telefone) da planilha.</li>
        </ol>
    </div>

    <form method="post" action="index.php?action=import_csv" autocomplete="off" enctype="multipart/form-data">
        
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <div class="mb-3">
            <label for="csv_file" class="form-label fw-bold">Ficheiro CSV *</label>
            <input 
                type="file" 
                class="form-control" 
                name="csv_file" 
                id="csv_file"
                accept=".csv" 
                required>
        </div>

        <button type="submit" class="btn btn-primary mt-3 w-100 fw-bold">
            <i class="bi bi-check-circle"></i> Iniciar Importação
        </button>
    </form>
</div>