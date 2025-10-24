<?php
// API de teste simples para verificar problemas de conexão
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Função para retornar resposta JSON
function jsonResponse($success, $data = null, $error = null) {
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

try {
    // Teste 1: Verificar se arquivo de conexão existe
    $conexaoFile = __DIR__ . '/../model/Conexao.php';
    if (!file_exists($conexaoFile)) {
        jsonResponse(false, null, 'Arquivo de conexão não encontrado: ' . $conexaoFile);
    }

    // Teste 2: Incluir arquivo de conexão
    require_once $conexaoFile;

    // Teste 3: Criar instância da conexão
    $conexao = Conexao::getInstance();
    $conn = $conexao->getConnection();

    // Teste 4: Executar query simples
    $stmt = $conn->query("SELECT 1 as teste, NOW() as agora");
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Teste 5: Verificar tabelas (opcional)
    $tabelas = [];
    try {
        $stmt = $conn->query("SHOW TABLES");
        $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        // Tabelas podem não existir ainda
        $tabelas = ['erro' => $e->getMessage()];
    }

    // Retornar sucesso com informações
    jsonResponse(true, [
        'conexao' => 'OK',
        'query_teste' => $resultado,
        'tabelas' => $tabelas,
        'php_version' => PHP_VERSION,
        'host' => $_SERVER['HTTP_HOST'] ?? 'desconhecido'
    ]);

} catch (Exception $e) {
    jsonResponse(false, null, [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
