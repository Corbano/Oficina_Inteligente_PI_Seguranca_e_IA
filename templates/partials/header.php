<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina Inteligente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Link para o seu CSS (que contém o 'bg-bic-blue') -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- CSS para o Rodapé "Colado" (Sticky Footer) -->
    <style>
        /* A Solução Limpa para o "Sticky Footer" sem dependências de HTML */
        body {
            /* Define o 'grid' (grelha) */
            display: grid;
            /* Define 3 linhas: cabeçalho(auto), conteúdo(1fr), rodapé(auto) */
            grid-template-rows: auto 1fr auto; 
            /* Altura mínima de 100% do ecrã */
            min-height: 100vh;
            /* Remove margens do body */
            margin: 0; 
        }

        /* O <main> (conteúdo) vai crescer para preencher o espaço (1fr) */
        main {
            /* O padding que estava no seu container-fluid (py-4) */
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
    </style>
</head>
<body>

<!-- O 'wrapper' do grid é o próprio <body> -->

<?php if (isset($_SESSION['user_id'])): ?>
<!-- O <nav> agora está dentro de um <header> para o grid o identificar -->
<header>
    <!-- ESTA É A SUA BARRA AZUL -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-bic-blue shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?page=dashboard">
                <i class="bi bi-tools"></i> Oficina Inteligente
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <span class="nav-link">
                            Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilizador'); ?>!
                        </span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=do_logout">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>
<?php endif; ?>

<!-- O conteúdo principal (o py-4 foi movido para o CSS) -->
<main class="container-fluid">
    <!-- O seu index.php vai incluir o 'add_cliente_form.php' etc. AQUI -->