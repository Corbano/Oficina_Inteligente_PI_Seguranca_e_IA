<?php
// alertas.php
// Busca os alertas do microserviço Python na porta 5002
$json = @file_get_contents("http://127.0.0.1:5002/alertas");

if ($json === false) {
    echo '<div class="alert alert-danger">Não foi possível carregar os alertas.</div>';
    return;
}

// Decodifica o JSON em array associativo
$alertas = json_decode($json, true);
$total_alertas = count($alertas);
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title text-bic-blue">
            <i class="bi bi-bell-fill text-mustard-yellow"></i> Alertas de Manutenção
            <span class="badge bg-danger"><?php echo $total_alertas; ?></span>
        </h5>
        <?php if ($total_alertas === 0): ?>
            <p class="text-muted">Nenhum alerta de manutenção no momento.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach($alertas as $a): 
                    // Número de telefone de exemplo; você pode adaptar para cada cliente
                    $telefone_api = preg_replace('/[^0-9]/', '', '5511999999999');
                    $mensagem_url = urlencode($a['mensagem']);
                    $link_whatsapp = "https://wa.me/{$telefone_api}?text={$mensagem_url}";
                ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-mustard">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title text-bic-blue fw-bold">
                                    <?php echo htmlspecialchars($a['veiculo']); ?>  
                                    - <span class="badge bg-dark"><?php echo htmlspecialchars($a['placa']); ?></span>
                                </h6>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($a['cliente']); ?></p>
                                <p class="card-text">
                                    <strong class="text-mustard-yellow">Nível: <?php echo htmlspecialchars($a['nivel']); ?></strong><br>
                                    <small>Próxima manutenção: <?php echo date('d/m/Y', strtotime($a['prox_troca'])); ?></small>
                                </p>
                                <a href="<?php echo $link_whatsapp; ?>" target="_blank" class="btn btn-success mt-auto">
                                    <i class="bi bi-whatsapp"></i> Enviar Lembrete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
