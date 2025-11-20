<?php
// 1. Inicia a sessão para poder acedê-la
session_start();

// 2. Limpa todas as variáveis da sessão (ex: $_SESSION['user_id'])
$_SESSION = [];

// 3. Destrói completamente a sessão
session_destroy();

// 4. Redireciona o utilizador para a página de login
//    (Presumo que o seu index.php redireciona para o login se não houver sessão)
header('Location: index.php');

// 5. Para o script imediatamente para garantir o redirecionamento
exit();
?>