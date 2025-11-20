<?php
// templates/ultimos_servicos.php
require __DIR__ . '/../src/auth/auth_checker.php';
require __DIR__ . '/../config/db.php';

if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>

<h1 class="mb-4 text-bic-blue">Últimos Serviços</h1>

<div class="row">
<?php
try {
    $query = $pdo->query("
        SELECT 
            s.tipo_servico, 
            s.data_servico,
            c.nome AS cliente_nome,
            v.modelo,
            v.placa
        FROM 
            servicos s
        LEFT JOIN 
            clientes c ON s.cliente_id = c.id
        LEFT JOIN 
            veiculos v ON s.veiculo_id = v.id
        ORDER BY 
            s.data_servico DESC
        LIMIT 10
    ");

    $servicos = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($servicos)) {
        echo "<div class='col-12'><div class='alert alert-secondary'>Nenhum serviço registrado recentemente.</div></div>";
    } else {
        foreach ($servicos as $s):
?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm border-left-bic-blue">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-bic-blue fw-bold">
                    <?= e($s['modelo'] ?? 'Veículo não informado'); ?>
                    <?php if (!empty($s['placa'])): ?>
                        - <span class="badge bg-dark"><?= e($s['placa']); ?></span>
                    <?php endif; ?>
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    <?= e($s['cliente_nome'] ?? 'Cliente não encontrado'); ?>
                </h6>
                <p class="card-text">
                    Serviço: <?= e($s['tipo_servico']); ?><br>
                    Data: <?= date('d/m/Y', strtotime($s['data_servico'])); ?>
                </p>
            </div>
        </div>
    </div>
<?php
        endforeach;
    }

} catch (Exception $e) {
    echo "<div class='col-12'><div class='alert alert-danger'>Erro ao carregar serviços.</div></div>";
    error_log($e->getMessage());
}
?>
</div>
