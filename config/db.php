<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://nexusprime22.com.br/oficina_inteligente/');
}

// 2. Credenciais do Banco de Dados de Produção
// Peça esses dados ao seu provedor de hospedagem.
$host = 'localhost'; // Geralmente 'localhost', mas pode variar.
$dbname = 'oficina_db'; // Nome do banco de dados no servidor.
$user = 'oficina_user';       // Usuário do banco de dados no servidor.
$pass = 'Oficina@123';   // Senha do banco de dados no servidor.

// --- Conexão ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em produção, não exiba o erro detalhado. Logue o erro e mostre uma mensagem genérica.
    error_log('Erro de Conexão com o Banco de Dados: ' . $e->getMessage());
    // Você pode criar uma página bonita de erro e redirecionar para ela.
    die('Ocorreu um erro inesperado. Por favor, tente novamente mais tarde, ou entre em contato com o suporte.');
}
?>

